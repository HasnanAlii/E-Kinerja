<script>
function monitoringApp() {
    return {
        isCameraOpen: false, stream: null, liveCount: 0, cameraInterval: null,
        detectionLogs: [], hasZonas: false,
        now: Math.floor(Date.now() / 1000),
        durationMap: {},   // { pegawai_id: { base, anchoredAt, status } }
        stats: { total_hadir: {{ $data->where('jenis', 'hadir')->count() }}, total_keluar: 0, avg_stay_str: '0j 0m' },
        init() {
            this.fetchStats(); this.fetchLogs(); this.checkZonas();
            // API refresh every 5 seconds
            setInterval(() => { this.fetchStats(); this.fetchLogs(); }, 5000);
            // Live timer ticks every second
            setInterval(() => { this.now = Math.floor(Date.now() / 1000); }, 1000);
        },
        checkZonas() {
            fetch('http://localhost:5000/api/zones').then(r=>r.json())
                .then(d => { this.hasZonas = d.zones && d.zones.length > 0; }).catch(()=>{});
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
                    }
                }).catch(()=>{});
        },
        // Jika di_meja → hitung terus. Jika tidak → beku.
        liveDuration(log) {
            const e = this.durationMap[log.pegawai_id];
            if (!e) return '—';
            const elapsed = (e.status === 'di_meja')
                ? Math.max(0, this.now - e.baseTime)
                : 0;
            const total = e.base + elapsed;
            if (total === 0) return '< 1 Menit';
            const h = Math.floor(total / 3600);
            const m = Math.floor((total % 3600) / 60);
            if (h > 0) return `${h} Jam ${m} Menit`;
            if (m > 0) return `${m} Menit`;
            return '< 1 Menit';
        },

        openZonaSetup() { window.dispatchEvent(new CustomEvent('open-zona-modal')); },
        startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
                .then(s => {
                    this.stream = s; this.$refs.videoElement.srcObject = s; this.isCameraOpen = true;
                    const canvas = this.$refs.canvasElement, ctx = canvas.getContext('2d'), video = this.$refs.videoElement;
                    this.cameraInterval = setInterval(() => {
                        if (video.paused || video.ended) return;
                        canvas.width = video.videoWidth || 640; canvas.height = video.videoHeight || 480;
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        this.sendFrame(canvas.toDataURL('image/jpeg', 0.6), false);
                    }, 500);
                }).catch(() => { this.isCameraOpen = true; this.startSimulation(); });
        },
        startSimulation() {
            const canvas = this.$refs.canvasElement, ctx = canvas.getContext('2d');
            this.cameraInterval = setInterval(() => {
                canvas.width = 640; canvas.height = 480;
                ctx.fillStyle = '#0f172a'; ctx.fillRect(0,0,640,480);
                ctx.strokeStyle='rgba(255,255,255,0.04)'; ctx.lineWidth=1;
                for(let i=0;i<640;i+=40){ctx.beginPath();ctx.moveTo(i,0);ctx.lineTo(i,480);ctx.stroke();}
                for(let j=0;j<480;j+=40){ctx.beginPath();ctx.moveTo(0,j);ctx.lineTo(640,j);ctx.stroke();}
                ctx.fillStyle='rgba(80,80,80,0.9)'; ctx.font='11px monospace';
                ctx.fillText('CCTV SIMULATION | '+new Date().toLocaleTimeString(), 10, 20);
                this.sendFrame(canvas.toDataURL('image/jpeg',0.6), true);
            }, 500);
        },
        sendFrame(dataUrl, sim) {
            fetch('http://localhost:5000/api/process-frame', {
                method:'POST', headers:{'Content-Type':'application/json'},
                body: JSON.stringify({ image: dataUrl, simulate: sim })
            }).then(r=>r.json()).then(d => {
                if (d.success) {
                    this.$refs.annotatedImg.src = d.annotated_image;
                    this.liveCount = d.detected_count || 0;
                    // NOTE: fetchLogs() NOT called here — avoid resetting duration anchors every 500ms
                    // Logs refresh via the 5s interval in init() instead
                }
            }).catch(()=>{});
        },
        stopCamera() {
            if (this.cameraInterval) { clearInterval(this.cameraInterval); this.cameraInterval = null; }
            if (this.stream) { this.stream.getTracks().forEach(t=>t.stop()); }
            this.$refs.videoElement.srcObject = null;
            this.$refs.annotatedImg.src = '';
            this.liveCount = 0; this.isCameraOpen = false;
        }
    };
}

function zonaSetupApp() {
    return {
        open: false, employees: [], selectedPegawai: null,
        drawing: false, startX: 0, startY: 0, currentRect: null, canvasImg: null,
        saving: false, deleting: false, statusMsg: '', statusOk: true, _t: null,
        initZona() {},
        async openModal() {
            this.open = true;
            await this.loadEmployees();
            this.$nextTick(() => this.initCanvas());
        },
        closeModal() { this.open = false; this.selectedPegawai = null; this.currentRect = null; this.statusMsg = ''; },
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
            ctx.fillStyle = '#0f172a'; ctx.fillRect(0,0,CW,CH);
            ctx.strokeStyle='rgba(255,255,255,0.05)'; ctx.lineWidth=1;
            for(let i=0;i<CW;i+=40){ctx.beginPath();ctx.moveTo(i,0);ctx.lineTo(i,CH);ctx.stroke();}
            for(let j=0;j<CH;j+=40){ctx.beginPath();ctx.moveTo(0,j);ctx.lineTo(CW,j);ctx.stroke();}
            ctx.fillStyle='rgba(100,100,100,0.7)'; ctx.font='13px monospace';
            ctx.fillText('Area Kamera — Tandai posisi duduk setiap pegawai di sini', 14, 24);
            this.employees.filter(e=>e.has_zone).forEach((e) => {
                const x1=e.x1_pct/100*CW, y1=e.y1_pct/100*CH, x2=e.x2_pct/100*CW, y2=e.y2_pct/100*CH;
                ctx.strokeStyle=e.warna; ctx.lineWidth=2; ctx.strokeRect(x1,y1,x2-x1,y2-y1);
                ctx.fillStyle=e.warna+'22'; ctx.fillRect(x1,y1,x2-x1,y2-y1);
                ctx.fillStyle=e.warna; ctx.fillRect(x1,y1,x2-x1,22);
                ctx.fillStyle='#000'; ctx.font='bold 12px sans-serif';
                ctx.fillText(e.pegawai_name.split(' ')[0], x1+5, y1+15);
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
            if(this.canvasImg){const img=new Image();img.src=this.canvasImg;ctx.drawImage(img,0,0,CW,CH);}
            const x=Math.min(this.startX,pos.x), y=Math.min(this.startY,pos.y), w=Math.abs(pos.x-this.startX), h=Math.abs(pos.y-this.startY);
            const color=this.selectedPegawai.warna||'#2ECC71';
            ctx.strokeStyle=color; ctx.lineWidth=2; ctx.setLineDash([6,3]); ctx.strokeRect(x,y,w,h);
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
                if(d.success){this.showStatus('✓ '+d.message,true);this.currentRect=null;await this.loadEmployees();this.$nextTick(()=>this.initCanvas());}
                else{this.showStatus('✗ '+d.error,false);}
            }catch(e){this.showStatus('✗ Gagal menyimpan',false);}finally{this.saving=false;}
        },
        async deleteZone() {
            if(!this.selectedPegawai?.has_zone)return; this.deleting=true;
            try {
                const d=await fetch('http://localhost:5000/api/zones/'+this.selectedPegawai.pegawai_id,{method:'DELETE'}).then(r=>r.json());
                if(d.success){this.showStatus('Zona dihapus',true);this.selectedPegawai=null;await this.loadEmployees();this.$nextTick(()=>this.initCanvas());}
            }catch(e){this.showStatus('Gagal',false);}finally{this.deleting=false;}
        },
        showStatus(msg,ok){this.statusMsg=msg;this.statusOk=ok;clearTimeout(this._t);this._t=setTimeout(()=>{this.statusMsg='';},4000);}
    };
}
</script>