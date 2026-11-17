<script>
function monitoringApp() {
    return {
        isCameraOpen: false, isSimulating: false, isFullscreen: false, stream: null, liveCount: 0, cameraInterval: null,
        isProcessingFrame: false,
        zonesList: [], latestDetections: [],
        detectionLogs: [], hasZonas: false,
        zoneWarningOpen: false,
        infoModalOpen: false, infoLoading: false, infoLog: null, infoData: null,
        now: Math.floor(Date.now() / 1000),
        durationMap: {},   // { pegawai_id: { base, anchoredAt, status } }
        stats: { total_hadir: {{ $data->where('jenis', 'hadir')->count() }}, total_keluar: 0, avg_stay_str: '0j 0m' },
        init() {
            this.fetchStats(); this.fetchLogs(); this.checkZonas();
            window.addEventListener('zones-updated', () => this.checkZonas());
            window.addEventListener('resize', () => {
                if (this.isCameraOpen && !this.isSimulating) this.drawOverlay();
            });
            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
                setTimeout(() => {
                    if (this.isCameraOpen && !this.isSimulating) this.drawOverlay();
                }, 100);
            });

            // Auto-resume if monitoring was already started (survives reload, navigation, or logout/re-login)
            this.syncMonitoringState();

            // API refresh every 5 seconds
            setInterval(() => { this.fetchStats(); this.fetchLogs(); }, 5000);
            // Live timer ticks every second
            setInterval(() => { this.now = Math.floor(Date.now() / 1000); }, 1000);
        },
        syncMonitoringState() {
            fetch('http://localhost:5000/api/monitoring-status')
                .then(r => r.json())
                .then(state => {
                    if (state && state.active) {
                        localStorage.setItem('cctv_monitoring_active', '1');
                        this.liveCount = state.detected_count || 0;
                        this.latestDetections = state.detections || [];
                        if (!this.isCameraOpen) {
                            this.startCamera(true);
                        }
                    } else if (localStorage.getItem('cctv_monitoring_active') === '1') {
                        // Server was restarted while monitoring was marked active in browser
                        if (!this.isCameraOpen) {
                            this.startCamera(true);
                        }
                    }
                })
                .catch(() => {
                    if (localStorage.getItem('cctv_monitoring_active') === '1' && !this.isCameraOpen) {
                        this.startCamera(true);
                    }
                });
        },
        checkZonas() {
            fetch('http://localhost:5000/api/zones').then(r=>r.json())
                .then(d => {
                    this.zonesList = d.zones || [];
                    this.hasZonas = this.zonesList.length > 0;
                    if (this.isCameraOpen && !this.isSimulating) {
                        this.drawOverlay();
                    }
                }).catch(()=>{});
        },
        fetchStats() {
            fetch('http://localhost:5000/api/stats').then(r=>r.json())
                .then(d => { if (d && !d.error) this.stats = d; }).catch(()=>{});
        },
        fetchLogs() {
            fetch('http://localhost:5000/api/log-deteksi').then(r=>r.json())
                .then(d => {
                    if (Array.isArray(d)) {
                        const nowSec = Math.floor(Date.now() / 1000);
                        d.forEach(log => {
                            const apiSec  = log.durasi_stay_seconds || 0;
                            const existing = this.durationMap[log.pegawai_id];

                            if (!existing) {
                                // First time: anchor from API value
                                this.durationMap[log.pegawai_id] = {
                                    base: apiSec, baseTime: nowSec, status: log.status_raw
                                };
                                return;
                            }

                            if (log.status_raw !== existing.status) {
                                // Status changed (e.g. di_meja → keluar) — hard reset
                                this.durationMap[log.pegawai_id] = {
                                    base: apiSec, baseTime: nowSec, status: log.status_raw
                                };
                                return;
                            }

                            // Same status — check if our running estimate drifts from API by > 5s
                            const myEstimate = existing.base + (existing.status === 'di_meja'
                                ? nowSec - existing.baseTime : 0);
                            if (Math.abs(apiSec - myEstimate) > 5) {
                                // Re-anchor to API value while keeping smooth count
                                existing.base    = apiSec;
                                existing.baseTime = nowSec;
                            }
                            existing.status = log.status_raw;
                        });
                        this.detectionLogs = d;
                        if (this.infoLog) {
                            const updated = d.find(x => x.pegawai_id === this.infoLog.pegawai_id);
                            if (updated) this.infoLog = updated;
                        }
                    }
                }).catch(()=>{});
        },
        liveSeconds(log) {
            if (!log) return 0;
            const e = this.durationMap[log.pegawai_id];
            if (!e) return log.durasi_stay_seconds || 0;
            const elapsed = (e.status === 'di_meja')
                ? Math.max(0, this.now - e.baseTime)
                : 0;
            return e.base + elapsed;
        },
        // Jika di_meja → hitung terus. Jika tidak → beku.
        liveDuration(log) {
            if (!log) return '—';
            const total = this.liveSeconds(log);
            if (total === 0) return '< 1 Menit';
            const h = Math.floor(total / 3600);
            const m = Math.floor((total % 3600) / 60);
            if (h > 0) return `${h} Jam ${m} Menit`;
            if (m > 0) return `${m} Menit`;
            return '< 1 Menit';
        },
        liveProgressPct(log) {
            const total = this.liveSeconds(log);
            const STANDAR_DETIK = 8 * 3600; // 8 Jam = 28800 detik
            return Math.min(100, +((total / STANDAR_DETIK) * 100).toFixed(1));
        },
        remainingTo8Hours(log) {
            const total = this.liveSeconds(log);
            const STANDAR_DETIK = 8 * 3600;
            const rem = Math.max(0, STANDAR_DETIK - total);
            if (rem === 0) return 'Target 8 Jam Tercapai ✓';
            const h = Math.floor(rem / 3600);
            const m = Math.ceil((rem % 3600) / 60);
            if (h > 0) return `${h} Jam ${m} Menit lagi`;
            return `${m} Menit lagi`;
        },
        openInfoModal(log) {
            this.infoLog = log;
            this.infoModalOpen = true;
            this.infoLoading = true;
            const today = new Date().toISOString().slice(0, 10);
            fetch(`http://localhost:5000/api/efektivitas/${log.pegawai_id}/${today}`)
                .then(r => r.json())
                .then(d => { this.infoData = d; })
                .catch(() => {
                    this.infoData = {
                        nama: log.nama,
                        tanggal_label: today,
                        check_in: '-',
                        check_out: '-',
                        durasi_di_meja: this.liveDuration(log)
                    };
                })
                .finally(() => { this.infoLoading = false; });
        },
        closeInfoModal() {
            this.infoModalOpen = false;
            this.infoLog = null;
            this.infoData = null;
        },

        openZonaSetup() { window.dispatchEvent(new CustomEvent('open-zona-modal')); },
        toggleFullscreen() {
            const wrapper = this.$refs.videoWrapper;
            if (!wrapper) return;
            if (!document.fullscreenElement) {
                wrapper.requestFullscreen?.().catch(() => {});
            } else {
                document.exitFullscreen?.().catch(() => {});
            }
        },
        async startCamera(isAutoResume = false) {
            try {
                const zd = await fetch('http://localhost:5000/api/zones').then(r => r.json());
                this.zonesList = zd.zones || [];
                this.hasZonas = this.zonesList.length > 0;
            } catch (e) {}

            if (!this.hasZonas) {
                localStorage.removeItem('cctv_monitoring_active');
                if (!isAutoResume) {
                    this.zoneWarningOpen = true;
                }
                return;
            }

            localStorage.setItem('cctv_monitoring_active', '1');
            navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 1280 }, height: { ideal: 720 }, frameRate: { ideal: 30 } }
            })
                .then(s => {
                    this.stream = s;
                    this.isSimulating = false;
                    this.isCameraOpen = true;
                    fetch('http://localhost:5000/api/start-monitoring', {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ simulate: false })
                    }).catch(() => {});
                    const video = this.$refs.videoElement;
                    video.srcObject = s;
                    video.onloadedmetadata = () => {
                        this.drawOverlay();
                        this.loopCapture();
                    };
                }).catch(() => {
                    this.isSimulating = true;
                    this.isCameraOpen = true;
                    fetch('http://localhost:5000/api/start-monitoring', {
                        method: 'POST', headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ simulate: true })
                    }).catch(() => {});
                    this.startSimulation();
                });
        },
        loopCapture() {
            if (!this.isCameraOpen || this.isSimulating) return;
            const video = this.$refs.videoElement;
            const canvas = this.$refs.canvasElement;
            if (video && !video.paused && !video.ended && !this.isProcessingFrame) {
                const ctx = canvas.getContext('2d');
                // Downscale frame for fast transmission & YOLO inference
                canvas.width = 480;
                canvas.height = Math.round((video.videoHeight / (video.videoWidth || 640)) * 480) || 270;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                this.sendFrame(canvas.toDataURL('image/jpeg', 0.55), false);
            } else {
                this.cameraInterval = setTimeout(() => this.loopCapture(), 120);
            }
        },
        startSimulation() {
            const canvas = this.$refs.canvasElement, ctx = canvas.getContext('2d');
            const runSim = () => {
                if (!this.isCameraOpen || !this.isSimulating) return;
                if (!this.isProcessingFrame) {
                    canvas.width = 640; canvas.height = 360;
                    ctx.fillStyle = '#0f172a'; ctx.fillRect(0,0,640,360);
                    ctx.strokeStyle='rgba(255,255,255,0.04)'; ctx.lineWidth=1;
                    for(let i=0;i<640;i+=40){ctx.beginPath();ctx.moveTo(i,0);ctx.lineTo(i,360);ctx.stroke();}
                    for(let j=0;j<360;j+=40){ctx.beginPath();ctx.moveTo(0,j);ctx.lineTo(640,j);ctx.stroke();}
                    ctx.fillStyle='rgba(80,80,80,0.9)'; ctx.font='11px monospace';
                    ctx.fillText('CCTV SIMULATION | '+new Date().toLocaleTimeString(), 10, 20);
                    this.sendFrame(canvas.toDataURL('image/jpeg',0.6), true);
                } else {
                    this.cameraInterval = setTimeout(runSim, 250);
                }
            };
            runSim();
        },
        sendFrame(dataUrl, sim) {
            this.isProcessingFrame = true;
            fetch('http://localhost:5000/api/process-frame', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body: JSON.stringify({ image: dataUrl, simulate: sim, include_image: sim })
            }).then(r=>r.json()).then(d => {
                if (d.success) {
                    this.liveCount = d.detected_count || 0;
                    this.latestDetections = d.detections || [];
                    if (sim && d.annotated_image) {
                        this.$refs.annotatedImg.src = d.annotated_image;
                    } else {
                        this.drawOverlay();
                    }
                }
            }).catch(()=>{}).finally(() => {
                this.isProcessingFrame = false;
                if (this.isCameraOpen) {
                    this.cameraInterval = setTimeout(() => {
                        if (sim) this.startSimulation();
                        else this.loopCapture();
                    }, 180);
                }
            });
        },
        drawOverlay() {
            const overlay = this.$refs.overlayCanvas;
            const video = this.$refs.videoElement;
            if (!overlay || !video) return;
            const W = overlay.clientWidth || 640;
            const H = overlay.clientHeight || 360;
            overlay.width = W;
            overlay.height = H;
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, W, H);

            const activeIds = new Set(
                (this.latestDetections || []).filter(d => d.pegawai_id).map(d => d.pegawai_id)
            );

            // 1. Draw configured zones
            (this.zonesList || []).forEach(z => {
                const x1 = (z.x1_pct / 100) * W;
                const y1 = (z.y1_pct / 100) * H;
                const zw = ((z.x2_pct - z.x1_pct) / 100) * W;
                const zh = ((z.y2_pct - z.y1_pct) / 100) * H;
                const color = z.warna || '#2ECC71';
                const isActive = activeIds.has(z.pegawai_id);

                ctx.fillStyle = color + (isActive ? '28' : '14');
                ctx.fillRect(x1, y1, zw, zh);

                ctx.strokeStyle = color;
                ctx.lineWidth = isActive ? 3 : 1.5;
                ctx.strokeRect(x1, y1, zw, zh);

                const firstName = (z.pegawai_name || 'Pegawai').split(' ')[0];
                const rawLabel = (z.label || '').trim();
                const label = rawLabel
                    ? (rawLabel.toLowerCase().includes(firstName.toLowerCase()) ? rawLabel : `${rawLabel}: ${firstName}`)
                    : `Meja ${firstName}`;
                ctx.fillStyle = color;
                ctx.fillRect(x1, y1, zw, 24);
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 12px sans-serif';
                ctx.fillText(label, x1 + 6, y1 + 16);
            });

            // 2. Draw AI person bounding boxes
            (this.latestDetections || []).forEach(det => {
                if (!det.box_pct) return;
                const bx1 = (det.box_pct.x1 / 100) * W;
                const by1 = (det.box_pct.y1 / 100) * H;
                const bw = ((det.box_pct.x2 - det.box_pct.x1) / 100) * W;
                const bh = ((det.box_pct.y2 - det.box_pct.y1) / 100) * H;

                const matchedZone = (this.zonesList || []).find(z => z.pegawai_id === det.pegawai_id);
                const color = matchedZone ? (matchedZone.warna || '#2ECC71') : '#94a3b8';

                ctx.strokeStyle = color;
                ctx.lineWidth = 2.5;
                ctx.strokeRect(bx1, by1, bw, bh);

                const pct = Math.round((det.confidence || 0) * 100);
                const text = `${det.pegawai_name || 'Terdeteksi'} (${pct}%)`;
                ctx.font = 'bold 12px sans-serif';
                const tw = ctx.measureText(text).width;
                const barY = Math.max(0, by1 + bh - 22);
                ctx.fillStyle = color;
                ctx.fillRect(bx1, barY, tw + 12, 22);
                ctx.fillStyle = '#000000';
                ctx.fillText(text, bx1 + 6, barY + 15);
            });
        },
        stopCamera() {
            localStorage.removeItem('cctv_monitoring_active');
            this.isCameraOpen = false;
            this.isSimulating = false;
            if (this.cameraInterval) { clearTimeout(this.cameraInterval); clearInterval(this.cameraInterval); this.cameraInterval = null; }
            if (this.stream) { this.stream.getTracks().forEach(t=>t.stop()); this.stream = null; }
            if (this.$refs.videoElement) this.$refs.videoElement.srcObject = null;
            if (this.$refs.annotatedImg) this.$refs.annotatedImg.src = '';
            if (this.$refs.overlayCanvas) {
                const ctx = this.$refs.overlayCanvas.getContext('2d');
                ctx.clearRect(0, 0, this.$refs.overlayCanvas.width, this.$refs.overlayCanvas.height);
            }
            this.liveCount = 0;
            this.latestDetections = [];

            // Reset status CCTV di backend agar kembali ke 'belum_terdeteksi'
            fetch('http://localhost:5000/api/stop-monitoring', { method: 'POST' })
                .then(r => r.json())
                .then(() => {
                    this.durationMap = {};
                    this.fetchLogs();
                })
                .catch(() => {
                    this.durationMap = {};
                    this.fetchLogs();
                });
        }
    };
}

function zonaSetupApp() {
    return {
        open: false, employees: [], selectedPegawai: null,
        drawing: false, startX: 0, startY: 0, currentRect: null, canvasImg: null,
        previewStream: null, previewCameraReady: false,
        saving: false, deleting: false, statusMsg: '', statusOk: true, _t: null,
        initZona() {},
        async openModal() {
            this.open = true;
            await this.loadEmployees();
            this.$nextTick(() => {
                this.startPreviewCamera();
                this.initCanvas();
            });
        },
        closeModal() {
            this.stopPreviewCamera();
            this.open = false;
            this.selectedPegawai = null;
            this.currentRect = null;
            this.statusMsg = '';
        },
        startPreviewCamera() {
            const video = document.getElementById('zonaPreviewVideo');
            if (!video) return;
            navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 1280 }, height: { ideal: 720 } }
            }).then(s => {
                if (!this.open) {
                    s.getTracks().forEach(t => t.stop());
                    return;
                }
                this.previewStream = s;
                video.srcObject = s;
                this.previewCameraReady = true;
                video.onloadedmetadata = () => this.initCanvas();
            }).catch(() => {
                this.previewCameraReady = false;
                this.initCanvas();
            });
        },
        stopPreviewCamera() {
            if (this.previewStream) {
                this.previewStream.getTracks().forEach(t => t.stop());
                this.previewStream = null;
            }
            const video = document.getElementById('zonaPreviewVideo');
            if (video) video.srcObject = null;
            this.previewCameraReady = false;
        },
        async loadEmployees() {
            try {
                const d = await fetch('http://localhost:5000/api/zones').then(r=>r.json());
                const configured = (d.zones||[]).map(z => ({
                    pegawai_id: z.pegawai_id, pegawai_name: z.pegawai_name, jabatan: z.jabatan,
                    has_zone: true, zona_label: z.label, warna: z.warna,
                    x1_pct: z.x1_pct, y1_pct: z.y1_pct, x2_pct: z.x2_pct, y2_pct: z.y2_pct
                }));
                const unzoned = (d.unzoned||[]).map(u => ({
                    pegawai_id: u.pegawai_id, pegawai_name: u.pegawai_name, jabatan: u.jabatan,
                    has_zone: false, zona_label: null, warna: u.warna
                }));
                this.employees = [...configured, ...unzoned];
            } catch(e) {}
        },
        initCanvas() {
            const canvas = document.getElementById('zonaDrawCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const CW = canvas.offsetWidth || 640, CH = Math.max(canvas.offsetHeight||0, 300);
            canvas.width = CW; canvas.height = CH;
            ctx.clearRect(0, 0, CW, CH);

            if (!this.previewCameraReady) {
                ctx.fillStyle = '#0f172a'; ctx.fillRect(0,0,CW,CH);
                ctx.strokeStyle='rgba(255,255,255,0.05)'; ctx.lineWidth=1;
                for(let i=0;i<CW;i+=40){ctx.beginPath();ctx.moveTo(i,0);ctx.lineTo(i,CH);ctx.stroke();}
                for(let j=0;j<CH;j+=40){ctx.beginPath();ctx.moveTo(0,j);ctx.lineTo(CW,j);ctx.stroke();}
            }

            ctx.fillStyle='rgba(0,0,0,0.55)';
            ctx.fillRect(8, 8, 430, 24);
            ctx.fillStyle='#e2e8f0'; ctx.font='12px monospace';
            ctx.fillText('Live Kamera — Klik & drag untuk menandai posisi duduk pegawai', 14, 24);

            this.employees.filter(e=>e.has_zone).forEach((e) => {
                const x1=e.x1_pct/100*CW, y1=e.y1_pct/100*CH, x2=e.x2_pct/100*CW, y2=e.y2_pct/100*CH;
                ctx.strokeStyle=e.warna; ctx.lineWidth=2.5; ctx.strokeRect(x1,y1,x2-x1,y2-y1);
                ctx.fillStyle=e.warna+'28'; ctx.fillRect(x1,y1,x2-x1,y2-y1);
                ctx.fillStyle=e.warna; ctx.fillRect(x1,y1,x2-x1,22);
                ctx.fillStyle='#fff'; ctx.font='bold 12px sans-serif';
                ctx.fillText(e.pegawai_name.split(' ')[0], x1+6, y1+15);
            });
            this.canvasImg = canvas.toDataURL();
        },
        getPos(e) {
            const canvas=document.getElementById('zonaDrawCanvas'), rect=canvas.getBoundingClientRect();
            return { x:(e.clientX-rect.left)*(canvas.width/rect.width), y:(e.clientY-rect.top)*(canvas.height/rect.height) };
        },
        startDraw(e) {
            if(!this.selectedPegawai) return;
            this.drawing=true; const p=this.getPos(e); this.startX=p.x; this.startY=p.y; this.currentRect=null;
        },
        updateDraw(e) {
            if(!this.drawing||!this.selectedPegawai) return;
            const canvas=document.getElementById('zonaDrawCanvas'), ctx=canvas.getContext('2d');
            const CW=canvas.width, CH=canvas.height, pos=this.getPos(e);
            ctx.clearRect(0, 0, CW, CH);
            if(this.canvasImg){const img=new Image();img.src=this.canvasImg;ctx.drawImage(img,0,0,CW,CH);}
            const x=Math.min(this.startX,pos.x), y=Math.min(this.startY,pos.y), w=Math.abs(pos.x-this.startX), h=Math.abs(pos.y-this.startY);
            const color=this.selectedPegawai.warna||'#2ECC71';
            ctx.strokeStyle=color; ctx.lineWidth=2.5; ctx.setLineDash([6,3]); ctx.strokeRect(x,y,w,h);
            ctx.fillStyle=color+'33'; ctx.fillRect(x,y,w,h); ctx.setLineDash([]);
            ctx.fillStyle=color; ctx.font='bold 13px sans-serif';
            ctx.fillText(this.selectedPegawai.pegawai_name.split(' ')[0], x+6, y+18);
        },
        endDraw(e) {
            if(!this.drawing||!this.selectedPegawai){return;}
            this.drawing=false;
            const canvas=document.getElementById('zonaDrawCanvas'), CW=canvas.width, CH=canvas.height, pos=this.getPos(e);
            const x1=Math.min(this.startX,pos.x), y1=Math.min(this.startY,pos.y), x2=Math.max(this.startX,pos.x), y2=Math.max(this.startY,pos.y);
            if((x2-x1)/CW<0.03||(y2-y1)/CH<0.03){this.currentRect=null;return;}
            this.currentRect={x1_pct:+((x1/CW)*100).toFixed(1),y1_pct:+((y1/CH)*100).toFixed(1),x2_pct:+((x2/CW)*100).toFixed(1),y2_pct:+((y2/CH)*100).toFixed(1)};
        },
        cancelDraw(){if(this.drawing){this.drawing=false;this.currentRect=null;}},
        selectEmployee(p){this.selectedPegawai=p;this.currentRect=null;this.statusMsg='';},
        async saveZone() {
            if(!this.selectedPegawai||!this.currentRect)return; this.saving=true;
            try {
                const d=await fetch('http://localhost:5000/api/zones',{method:'POST',headers:{'Content-Type':'application/json'},
                    body:JSON.stringify({pegawai_id:this.selectedPegawai.pegawai_id,...this.currentRect,label:'Meja '+this.selectedPegawai.pegawai_name.split(' ')[0]})}).then(r=>r.json());
                if(d.success){this.showStatus('✓ '+d.message,true);this.currentRect=null;await this.loadEmployees();this.$nextTick(()=>this.initCanvas());window.dispatchEvent(new CustomEvent('zones-updated'));}
                else{this.showStatus('✗ '+d.error,false);}
            }catch(e){this.showStatus('✗ Gagal menyimpan',false);}finally{this.saving=false;}
        },
        async deleteZone() {
            if(!this.selectedPegawai?.has_zone)return; this.deleting=true;
            try {
                const d=await fetch('http://localhost:5000/api/zones/'+this.selectedPegawai.pegawai_id,{method:'DELETE'}).then(r=>r.json());
                if(d.success){this.showStatus('Zona dihapus',true);this.selectedPegawai=null;await this.loadEmployees();this.$nextTick(()=>this.initCanvas());window.dispatchEvent(new CustomEvent('zones-updated'));}
            }catch(e){this.showStatus('Gagal',false);}finally{this.deleting=false;}
        },
        showStatus(msg,ok){this.statusMsg=msg;this.statusOk=ok;clearTimeout(this._t);this._t=setTimeout(()=>{this.statusMsg='';},4000);}
    };
}
</script>