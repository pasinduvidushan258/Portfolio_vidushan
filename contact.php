<section id="contact" class="page-section">
    <div class="section-wrapper">
        <div class="section-heading">
            <p class="sub-title">Get In Touch</p>
            <h2 class="main-title">Contact Me</h2>
        </div>

        <div class="contact-container">
            <div class="contact-sidebar">
                <div class="info-card">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-text">
                        <span>Email Me</span>
                        <p>pasinduvidushan258@gmail.com</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"><i class="fab fa-whatsapp"></i></div>
                    <div class="info-text">
                        <span>WhatsApp</span>
                        <p>+94 76 643 7197</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-text">
                        <span>Location</span>
                        <p>Colombo, Sri Lanka</p>
                    </div>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <form action="process_contact.php" method="POST">
                    <div class="form-row">
                        <div class="input-box">
                            <input type="text" name="name" required>
                            <label>Your Name</label>
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="input-box">
                            <input type="email" name="email" required>
                            <label>Your Email</label>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="input-box">
                            <input type="text" name="mobile" required>
                            <label>Mobile Number</label>
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="input-box">
                            <input type="text" name="subject" required>
                            <label>Subject</label>
                            <i class="fas fa-pen-nib"></i>
                        </div>
                    </div>

                    <div class="input-box">
                        <textarea name="message" rows="5" required></textarea>
                        <label>Your Message</label>
                    </div>
                    <button type="submit" class="submit-btn">
                        <span>Send Message</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    .contact-container { display: grid; grid-template-columns: 1fr 2fr; gap: 40px; margin-top: 40px; }
    .contact-sidebar { display: flex; flex-direction: column; gap: 20px; }
    .info-card { background: #1e293b; padding: 20px; border-radius: 16px; border: 1px solid #334155; display: flex; align-items: center; gap: 15px; transition: 0.3s; }
    .info-card:hover { transform: translateX(10px); border-color: #818cf8; }
    .info-icon { width: 50px; height: 50px; background: rgba(129, 140, 248, 0.1); color: #818cf8; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .info-text span { color: #94a3b8; font-size: 0.75rem; display: block; }
    .info-text p { color: #f8fafc; font-weight: 600; margin: 0; font-size: 0.9rem; }

    .contact-form-wrapper { background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(10px); padding: 40px; border-radius: 24px; border: 1px solid #334155; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .input-box { position: relative; margin-bottom: 30px; }
    .input-box input, .input-box textarea { width: 100%; padding: 12px 10px 12px 40px; background: #0f172a; border: 1px solid #334155; border-radius: 10px; color: #fff; outline: none; transition: 0.3s; }
    .input-box label { position: absolute; left: 40px; top: 12px; color: #64748b; transition: 0.3s; pointer-events: none; }
    .input-box i { position: absolute; left: 15px; top: 15px; color: #64748b; }
    
    .input-box input:focus ~ label, .input-box input:valid ~ label,
    .input-box textarea:focus ~ label, .input-box textarea:valid ~ label {
        top: -22px; left: 5px; font-size: 0.8rem; color: #818cf8;
    }
    .input-box input:focus, .input-box textarea:focus { border-color: #818cf8; box-shadow: 0 0 15px rgba(129, 140, 248, 0.2); }

    .submit-btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #6366f1, #a855f7); color: #fff; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: 0.3s; }
    .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4); }

    @media (max-width: 768px) { .contact-container { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } }
</style>