<?php
/*
 * Copyright 2010 - Robson Dantas <biu.dantas@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// require authentication
require_once("auth.php");

// modifying header, since this file outputs a jpeg image.
// TODO - check incoming header, and sent it. If orkut changes captcha to another format, will break this code
header("content-type: image/jpeg");

// captcha url
$captcha = $_GET["captchaUrl"];

$orkutApi->login();
$r = $orkutApi->executeCaptcha($captcha,'');

// output captcha image
echo $r['data'];

?>
