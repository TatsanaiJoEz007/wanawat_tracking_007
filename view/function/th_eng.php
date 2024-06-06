<?php
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'th';
    
    $th_language = array('th' => 'ภาษาไทย' , 'en' => 'TH Language');
    $lang_th_language = $th_language[$lang];

    $en_language = array('th' => 'ภาษาอังกฤษ' , 'en' => 'ENG Language'); // 
    $lang_en_language = $en_language[$lang];



    //Navbar Function

    $login_translate = array('th' => 'เข้าสู่ระบบ' , 'en' => 'Login');
    $lang_login = $login_translate[$lang];

    $register_translate = array('th' => 'ลงทะเบียน' , 'en' => 'Register');
    $lang_register = $register_translate[$lang];

    $question_translate = array('th' => 'คำถามที่พบบ่อย'  , 'en' => 'Frequency Asked Questions');
    $lang_question = $question_translate[$lang];

    $contact_translate = array('th' => 'ติดต่อเรา' , 'en' => 'Contact Us');
    $lang_contact = $contact_translate[$lang];

    $question_translate = array('th' => 'คำถามที่พบบ่อย' , 'en' => 'Frequency Asked Questions (FAQs)');
    $lang_question = $question_translate[$lang];

    $profile_translate = array('th' => 'ดูโปรไฟล์' , 'en' => 'View Profile');
    $lang_profile = $profile_translate [$lang];

    $logout_translate = array('th' => 'ออกจากระบบ' , 'en' => 'Logout');
    $lang_logout = $logout_translate[$lang];
    
    //End Navbar Function


    //Tracking Function

    $track_translate = array('th' => 'กรอกหมายเลขติดตามของคุณที่นี่!' , 'en' => 'Enter your tracking number here!');
    $lang_track = $track_translate[$lang];

    $ex_translate = array('th' => 'ตัวอย่าง' , 'en' => 'Ex.');
    $lang_ex = $track_translate[$lang];


    //End Tracking Function




    //Login Page 

    $email_translate = array('th' => 'อีเมลล์' , 'en' => 'Email');
    $lang_email = $email_translate[$lang];

    $password_translate = array('th' => 'รหัสผ่าน' , 'en' => 'Password');
    $lang_password = $password_translate[$lang];

    $login_translate = array('th' => 'เข้าสู่ระบบ' , 'en' => 'Login');
    $lang_login = $login_translate[$lang];

    $signup_translate = array('th' => 'สมัครสมาชิก' , 'en' => 'Sign up');
    $lang_signup = $signup_translate[$lang];

    $forgotpassword_translate = array('th' => 'ลืมรหัสผ่าน' , 'en' => 'Forgot password?');
    $lang_forgotpassword = $forgotpassword_translate[$lang];

    $donthaveaccount_translate = array('th' => 'หากยังไม่มีรหัสผ่านกด-->' , 'en' => 'Don&#10076;t have an account?');
    $lang_donthaveaccount = $donthaveaccount_translate[$lang];

    //end Login Page



    //register Page 
    $register_translate = array('th' => 'สมัครสมาชิก' , 'en' => 'Register');
    $lang_register = $register_translate[$lang];

    $fristname_translate = array('th' => 'ชื่อ' , 'en' => 'Firstname');
    $lang_fristname = $fristname_translate[$lang];

    $lastname_translate = array('th' => 'นามสกุล' , 'en' => 'Lastname');
    $lang_lastname = $lastname_translate[$lang];

    $address_translate = array('th' => 'ที่อยู่' , 'en' => 'Address');
    $lang_address= $address_translate[$lang];

    $addressph_translate = array('th' => 'ที่อยู่ (ไม่ต้องระบุตำบล อำเภอ จังหวัด)' , 'en' => 'Address (Do not specify sub-district, district, province)');
    $lang_addressph= $addressph_translate[$lang];

    $tel_translate = array('th' => 'เบอร์โทร' , 'en' => 'Telephone Number');
    $lang_tel = $tel_translate[$lang];

    $telph_translate = array('th' => 'เบอร์โทร (0XXXXXXXXX) ไม่ต้องใส่ขีด' , 'en' => '(0XXXXXXXXX) Do not use hyphens');
    $lang_telph = $telph_translate[$lang];

    $provinces_translate = array('th' => 'จังหวัด' , 'en' => 'Province');
    $lang_provinces = $provinces_translate[$lang];

    $provincesph_translate = array('th' => 'โปรดเลือกจังหวัด' , 'en' => 'Please choose Province');
    $lang_provincesph = $provincesph_translate[$lang];

    $amphures_translate = array('th' => 'อำเภอ' , 'en' => 'District');
    $lang_amphures = $amphures_translate[$lang];

    $amphuresph_translate = array('th' => 'โปรดเลือกอำเภอ' , 'en' => 'Please choose District');
    $lang_amphuresph = $amphuresph_translate[$lang];

    $districts_translate = array('th' => 'ตำบล' , 'en' => 'Subdistrict');
    $lang_districts = $districts_translate[$lang];

    $districtsph_translate = array('th' => 'โปรดเลือกตำบล' , 'en' => 'Please choose Subdistrict');
    $lang_districtsph = $districtsph_translate[$lang];

    $zipcode_translate = array('th' => 'รหัสไปรษณีย์' , 'en' => 'Zip Code');
    $lang_zipcode = $zipcode_translate[$lang];

    $confirmPassword_translate = array('th' => 'ยืนยันรหัสผ่าน' , 'en' => 'Confirm Password');
    $lang_confirmPassword = $confirmPassword_translate[$lang];

    $haveaccount_translate = array('th' => 'มีสมาชิกแล้วกด?? -->' , 'en' => 'Already have an account?');
    $lang_haveaccount= $haveaccount_translate[$lang];
    //end register Page 

    //Contact Page
    $contact_translate = array('th' => 'ติดต่อเรา' , 'en' => 'Contact us');
    $lang_contact = $contact_translate[$lang];

    $facebook_translate = array('th' => 'เฟสบุ๊ค' , 'en' => 'Facebook');
    $lang_facebook = $facebook_translate[$lang];

    $website_translate = array('th' => 'เว็บไซต์' , 'en' => 'Website');
    $lang_website = $website_translate[$lang];

    $call_translate = array('th' => 'โทร' , 'en' => 'Call');
    $lang_call = $call_translate [$lang];

    $askquestion_translate = array('th' => 'ถามคำถาม หรือแจ้งปัญหาในการใช้งาน' , 'en' => 'Ask a Question');
    $lang_askquestion = $askquestion_translate [$lang];

    $submit_translate = array('th' => 'ยืนยัน' , 'en' => 'Submit');
    $lang_submit = $submit_translate [$lang];

    $yourname_translate = array('th' => 'ชื่อผู้ส่ง' , 'en' => 'Yourname');
    $lang_yourname = $yourname_translate [$lang];

    $youremail_translate = array('th' => 'ใส่อีเมล์' , 'en' => 'YourEmail');
    $lang_youremail = $youremail_translate [$lang];

    $description_translate = array('th' => 'ใส่รายละเอียด' , 'en' => 'Description');
    $lang_description= $description_translate [$lang];


    //end Contact Page
    
    //question Page
    
    $freq_translate = array('th' => 'คำถามที่พบบ่อย' , 'en' => 'frequently questions');
    $lang_freq = $freq_translate  [$lang];

    
     // end question Page

     //profilepage
     $fullname_translate = array('th' => 'ชื่อ-นามสกุล' , 'en' => 'Fullname');
     $lang_fullname = $fullname_translate  [$lang];

     $email_translate = array('th' => 'อีเมลล์' , 'en' => 'Email');
     $lang_email = $email_translate  [$lang];

     $mobile_translate = array('th' => 'เบอร์โทรศัพท์' , 'en' => 'Mobile');
     $lang_mobile = $mobile_translate  [$lang];

     $address_translate = array('th' => 'ที่อยู่' , 'en' => 'Address');
     $lang_address = $address_translate  [$lang];


 
     //end profilepage



?>
