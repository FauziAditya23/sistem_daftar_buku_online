<div class="contact-dynamic-wrapper" style="width: 100%; min-height: 100vh; padding: 80px 20px; box-sizing: border-box; background-color: #f0f7ff; font-family: 'Inter', sans-serif; position: relative; overflow: hidden;">
    
    <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(0,123,255,0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: 10%; left: -50px; width: 200px; height: 200px; background: rgba(0,123,255,0.05); border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; transform: rotate(15deg);"></div>

    <div style="max-width: 1100px; margin: 0 auto; position: relative; z-index: 1;">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 50px; align-items: center;">
            
            <div style="padding-right: 20px;">
                <div style="display: inline-flex; align-items: center; gap: 10px; background: white; padding: 10px 20px; border-radius: 100px; box-shadow: 0 10px 20px rgba(0,0,0,0.03); margin-bottom: 30px;">
                    <span style="display: inline-block; width: 10px; height: 10px; background: #007bff; border-radius: 50%; box-shadow: 0 0 10px #007bff;"></span>
                    <span style="font-size: 13px; font-weight: 700; color: #007bff; letter-spacing: 1px;">Pusat Bantuan</span>
                </div>
                
                <h1 style="font-size: clamp(36px, 5vw, 54px); font-weight: 900; color: #1a202c; line-height: 1.1; margin: 0 0 25px 0;">
                    Mari berdiskusi <br>tentang <span style="color: #007bff; position: relative;">Ide Anda.
                        <svg style="position: absolute; bottom: -10px; left: 0; width: 100%; height: 12px;" viewBox="0 0 200 12" fill="none"><path d="M2 10C50 2 150 2 198 10" stroke="#007bff" stroke-width="4" stroke-linecap="round"/></svg>
                    </span>
                </h1>
                
                <p style="color: #64748b; font-size: 18px; line-height: 1.7; margin-bottom: 40px; max-width: 450px;">
                    Tim kami biasanya membalas dalam waktu kurang dari 24 jam. Jangan ragu untuk menyapa!
                </p>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div class="contact-icon-card" style="background: white; width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 15px 30px rgba(0,0,0,0.05); font-size: 24px;">📱</div>
                    <div class="contact-icon-card" style="background: white; width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 15px 30px rgba(0,0,0,0.05); font-size: 24px;">📧</div>
                    <div class="contact-icon-card" style="background: white; width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 15px 30px rgba(0,0,0,0.05); font-size: 24px;">🌐</div>
                </div>
            </div>

            <div style="position: relative;">
                <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: #ffd700; border-radius: 50%; z-index: -1; opacity: 0.5;"></div>
                
                <div style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); padding: 50px; border-radius: 40px; box-shadow: 0 40px 80px -15px rgba(0, 123, 255, 0.15); border: 1px solid rgba(255,255,255,0.5);">
                    <form action="#" method="POST" id="contactForm">
                        <div class="form-group" style="margin-bottom: 25px;">
                            <input type="text" placeholder="Nama Anda" 
                                style="width: 100%; padding: 18px 10px; border-radius: 20px; border: 2px solid transparent; background: #f0f7ff; outline: none; transition: 0.4s; font-size: 15px; font-weight: 500;"
                                class="dynamic-input">
                        </div>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <input type="email" placeholder="Email Akademik" 
                                style="width: 100%; padding: 18px 10px; border-radius: 20px; border: 2px solid transparent; background: #f0f7ff; outline: none; transition: 0.4s; font-size: 15px; font-weight: 500;"
                                class="dynamic-input">
                        </div>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <textarea rows="4" placeholder="Apa yang bisa kami bantu?" 
                                style="width: 100%; padding: 18px 10px; border-radius: 20px; border: 2px solid transparent; background: #f0f7ff; outline: none; transition: 0.4s; font-size: 15px; font-weight: 500; resize: none;"
                                class="dynamic-input"></textarea>
                        </div>

                        <button type="submit" style="width: 100%; padding: 20px; background: #007bff; color: white; border: none; border-radius: 20px; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.4s; box-shadow: 0 20px 40px rgba(0, 123, 255, 0.3); display: flex; align-items: center; justify-content: center; gap: 12px;"
                            onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 25px 50px rgba(0, 123, 255, 0.4)';" 
                            onmouseout="this.style.transform='translateY(0)';">
                            Kirim Pesan 
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animasi Input Dinamis */
    .dynamic-input:focus {
        border-color: #007bff !important;
        background: white !important;
        box-shadow: 0 10px 25px rgba(0, 123, 255, 0.08);
        transform: scale(1.02);
    }

    .contact-icon-card {
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
    }

    .contact-icon-card:hover {
        transform: translateY(-10px) rotate(10deg);
        background: #007bff !important;
        color: white !important;
    }

    /* Entry Animation */
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .contact-dynamic-wrapper > div {
        animation: slideUp 0.8s ease-out forwards;
    }
</style>