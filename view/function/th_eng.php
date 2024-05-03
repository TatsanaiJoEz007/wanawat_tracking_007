<?php
    $lang = isset($_GET['lang']) ? $_GET['lang'] : 'th';

    $login_translate = array('th' => 'เข้าสู่ระบบ' , 'en' => 'Login');
    $lang_login = $login_translate[lang];

    $register_translate = array('th' => 'ลงทะเบียน' , 'en' => 'Register');
    $lang_register = $register_translate[lang];

    $question_translate = array('th' => 'คำถามที่พบบ่อย'  , 'en' => 'Frequency Asked Questions');
    $lang_question = $question_translate[lang];

    $contact_translate = array('th' => 'ติดต่อเรา' , 'en' => 'Contact Us');
    $lang_contact = $contact_translate[lang];

    $question_translate = array('th' => 'ถามคำถาม หรือแจ้งปัญหาในการใช้งาน' , 'en' => 'Ask a question or report a problem');
    $lang_question = $question_translate[lang];

    $profile_translate = array('th' => 'ดูโปรไฟล์' , 'en' => 'View Profile');
    $lang_profile = $profile_translate [lang];

    $logout_translate = array('th' => 'ออกจากระบบ' , 'en' => 'Logout');
    $lang_logout = $logout_translate[lang];
    

?>