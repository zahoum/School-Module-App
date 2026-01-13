<?php
require 'config.php';

// تدمير جميع بيانات الجلسة
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول
header("Location: login.php");
exit();
?>