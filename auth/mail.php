<?php
ini_set("display_errors",0);
ob_start();
if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password    = $_POST['password'];
    $BASE_URL    = $_POST['BASE_URL'];
        ////////////////////////////////////////////email///////////////////////////////////////////////////////////////////////////////////////////////
                    $now = date("d-M-Y");
                    require_once('../../vendor/phpmailer/class.phpmailer.php');
                    require_once("../../vendor/phpmailer/class.smtp.php");    
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    $mail->Host = "mail.hikesoftwares.us";
                    $mail->SMTPAuth = true;
                    $mail->Username = "testmail@hikesoftwares.us";
                    $mail->Password = "Hike@#11";
                    $mail->From = "testmail@hikesoftwares.us";
                    $mail->FromName = "Engayg";
                    $mail->AddReplyTo("testmail@hikesoftwares.us", "Engayg");
                    $mail->Port = "587";
                    $mail->IsHTML(true);
                    $mail->Subject = 'ENGAYG - New password has been generated for your Engayg account.';
                    
                    $mail->Body = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
                    <html xmlns='http://www.w3.org/1999/xhtml'>
                       <head>
                          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                          <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
                       <title>Qyk - Password Reset.</title>
                          <style type='text/css'>
                             /* Client-specific Styles */
                             .bordr{border-left: 1px solid #000;border-right: 1px solid #000; width:600px; margin:0 auto;padding: 0px 0px;}
                             div, p, a, li, td { -webkit-text-size-adjust:none; }
                             #outlook a {padding:0;} /* Force Outlook to provide a 'view in browser' menu link. */
                             html{width: 100%; }
                             body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
                             /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
                             .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
                             .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing. */
                             #backgroundTable {margin:0;  width:100% !important; line-height: 100% !important;}
                             img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
                             a img {border:none;}
                             .image_fix {display:block;}
                             p {margin: 0px 0px !important;}
                             table td {border-collapse: collapse; vertical-align: top !important;}
                             table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
                             a {color: #33b9ff;text-decoration: none;text-decoration:none!important;}
                             /*STYLES*/
                             table[class=full] { width: 100%; clear: both; }
                             /*IPAD STYLES*/
                             @media only screen and (max-width: 640px) {
                             a[href^='tel'], a[href^='sms'] {
                             text-decoration: none;
                             color: #33b9ff; /* or whatever your want */
                             pointer-events: none;
                             cursor: default;
                             }
                             .mobile_link a[href^='tel'], .mobile_link a[href^='sms'] {
                             text-decoration: default;
                             color: #33b9ff !important;
                             pointer-events: auto;
                             cursor: default;
                             }
                             table[class=devicewidth] {width: 440px!important;text-align:center!important; }
                             table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
                             img[class=banner] {width: 440px!important;height:220px!important;}
                             img[class=col2img] {width: 440px!important;height:220px!important;}                  
                             }
                             /*IPHONE STYLES*/
                             @media only screen and (max-width: 480px) {
                             a[href^='tel'], a[href^='sms'] {
                             text-decoration: none;
                             color: #33b9ff; /* or whatever your want */
                             pointer-events: none;
                             cursor: default;
                             }
                             .mobile_link a[href^='tel'], .mobile_link a[href^='sms'] {
                             text-decoration: default;
                             color: #33b9ff !important; 
                             pointer-events: auto;
                             cursor: default;
                             }
                             table[class=devicewidth] {width: 280px!important;text-align:center!important;}
                             table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
                             img[class=banner] {width: 280px!important;height:140px!important;}
                             img[class=col2img] {width: 280px!important;height:140px!important;}                 
                             }
                          </style>
                       </head>
                       <body style='padding-top:80px; background-color:#fff;'>  
                     <!-- Start of header -->
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='header'  class='devicewidth1'>
                       <tbody>
                          <tr>
                             <td>                   
                              <table width='600' cellpadding='0' cellspacing='0' border='3' bordercolor='#3b5999' align='center' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td width='100%'>
                             <table width='600' cellpadding='0' cellspacing='0' border='0' bordercolor='#3b5999' align='center' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td width='100%'>
                    <!-- Start of Right Image -->      
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='right-image' >
                       <tbody>
                          <tr>
                             <td>
                                <table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td width='100%'>
                                            <table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                               <tbody>
                                                  <tr>
                                                     <td>
                                                        <!-- Start of left column -->
                                                        <table width='300' align='left' border='0' cellpadding='0' cellspacing='0' class='devicewidth' >
                                                           <tbody>
                                                              <!-- image -->
                                                              <tr>
                                                                 <td width='300' class='devicewidth'>
                                                                    <a href='#'><img src='".$BASE_URL."/uploads/images/logo.png' alt='' border='0' style='width: 71px;margin: 11px 17px 0px 17px;'></a>
                                                                 </td>
                                                              </tr>
                                                              <!-- /image -->
                                                           </tbody>
                                                        </table>
                                                        <!-- end of left column -->
                                                       </td>
                                                        <td align='right'>
                                                        <!-- start of right column -->
                                                        <table width='280' align='right' border='0' cellpadding='0' cellspacing='0' class='devicewidth' style='margin-right: 10px; margin-top: 5px;'>
                                                           <tbody>
                                                              <tr>
                                                                 <td align='right'>
                                                                    <!--<img src='".$BASE_URL."/uploads/images/seal.png' style='width: 80px;' />-->
                                                                 </td>
                                                              </tr>
                                                           </tbody>
                                                        </table>
                                                        <!-- end of right column -->
                                                     </td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                   </tbody>
                                </table>
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    <!-- End of Right Image -->
                    </td>
                    </tr>         
                    <tr>
                    <td width='100%'>         
                    <!-- Start of seperator -->
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='seperator'>
                       <tbody>
                          <tr>
                             <td>
                                <table width='600' align='center' cellspacing='0' cellpadding='0' border='0' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td align='center' height='10' style='font-size:1px; line-height:1px;'>&nbsp;</td>
                                      </tr>
                                   </tbody>
                                </table>
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    <!-- End of seperator --> 
                    </td>
                    </tr>         
                    <tr>
                    <td width='100%'>        
                    <!-- Start of header -->
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' st-sortable='header'>
                       <tbody>
                          <tr>
                             <td>
                                <table width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td width='100%'>
                                            <table width='580' bgcolor='#3b5999' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                               <tbody>
                                                  <tr>
                                                     <td>
                                                        <!-- start of menu -->
                                                        <table bgcolor='#3b5999' width='580' height='16' border='0' align='right' valign='middle' cellpadding='7' cellspacing='0' border='0' class='devicewidth'>
                                                           <tbody>
                                                              <tr>
                                                                 <td height='16' align='left' valign='middle' style='font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #282828' st-content='menu'>
                                                                    <a href='#' style='color: #fff;text-decoration: none; font-weight:bold;'>ENGAYG &nbsp; &nbsp;</a>                                                
                                                                 </td>
                                                                 <td height='16' align='right' valign='middle' style='font-family: Helvetica, arial, sans-serif; font-size: 13px;color: #282828' st-content='menu'>
                                                                    <a href='#' style='color: #fff;text-decoration: none; font-weight:bold;'>".$now." &nbsp; &nbsp;</a>                                                
                                                                 </td>                                             
                                                              </tr>
                                                           </tbody>
                                                        </table>
                                                        <!-- end of menu -->
                                                     </td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                   </tbody>
                                </table>
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    <!-- End of Header -->
                    </td>
                    </tr>         
                    <tr>
                    <td width='100%'>
                    <!-- Start of seperator -->
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='seperator'>
                       <tbody>
                          <tr>
                             <td>
                                <table width='600' align='center' cellspacing='0' cellpadding='0' border='0' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td align='center' height='10' style='font-size:1px; line-height:1px;'>&nbsp;</td>
                                      </tr>
                                   </tbody>
                                </table>
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    <!-- End of seperator --> 
                    </td>
                    </tr>         
                    <tr>
                    <td width='100%'>
                    <!-- Start of Right Image -->      
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='right-image'>
                       <tbody>
                          <tr>
                             <td>
                                <table width='580' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td width='100%'>
                                            <table width='580' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                               <tbody>
                                                  <tr>
                                                     <td>                                  
                                                        <!-- start of right column -->
                                                        <table width='580' height='158' align='right' bgcolor='#f5f5f5' border='0' cellpadding='0' cellspacing='0' class='devicewidth'>
                                                           <tbody>
                                                              <tr>
                                                                 <td>
                                                                    <table width='580' align='center' border='0' cellpadding='0' cellspacing='0' class='devicewidth'>
                                                                       <tbody>
                                                                          <!-- title -->
                                                                          <tr>
                                                                             <td>
                                                                               &nbsp;
                                                                             </td>
                                                                          </tr>
                                                                          <!-- end of title -->
                                                                          <!-- content -->
                                                                          <tr>
                                                                             <td style='font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #889098; text-align:left;padding-left:10px;'>
                                                                             <table>
                                                                                <tr height='25'>                                                                
                                                                                    <td align='left' valign='middle' height='25' style='color: #3b5999; font-family: corbel; font-size:17px; font-weight:bold;'>Dear ".$name."</td>
                                                                                </tr>
                                                                                <tr height='100'>                                                                
                                                                                    <td align='left' valign='middle' height='100' style='color: #444444; font-family: calibri; font-size:14px; line-height:18px; text-align:justify;padding-right:10px;'>
                                                                                        We received a request to reset your password for your Engayg account. Simply use the newly generated password for your next login.
                                                                                        <br /><br />                                                                    
                                                                                        New passowrd for your Engayg account <span style='color:#3b5999'><strong>".$password."</strong></span>, don't share this with anyone.
                                                                                        <br /><br />
                                                                                        If you did not request this Password, please let us know immediately at <a href='mailto:info@engayg.com' style='color: #3b5999;'>info@engayg.com</a>
                                                                                        <br /><br />
                                                                                        See you soon on Engayg.
                                                                                        <br /><br />                                            
                                                                                        Team Engayg<br />
                                                                                    </td>
                                                                                </tr>      
                                                                             </table>
                                                                             </td>
                                                                          </tr>
                                                                          <!-- end of content -->
                                                                          <!-- content -->
                                                                          <tr>
                                                                             <td style='font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #889098; text-align:left;padding-left:10px;'>
                                                                             <table height='35' width='120' align='right'>
                                                                                <tr height='35' width='120' align='right'>                                                                
                                                                                </tr>                                                               
                                                                             </table>
                                                                             </td>
                                                                          </tr>
                                                                          <!-- end of content -->
                                                                       </tbody>
                                                                    </table>
                                                                 </td>
                                                              </tr>
                                                           </tbody>
                                                        </table>
                                                        <!-- end of right column -->
                                                     </td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                   </tbody>
                                </table>
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    <!-- End of Right Image -->
                    </td>
                    </tr>         
                    <tr>
                    <td width='100%'>
                    <!-- Start of seperator -->
                    <table width='100%' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='seperator'>
                       <tbody>
                          <tr>
                             <td>
                                <table width='600' align='center' cellspacing='0' cellpadding='0' border='0' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td align='center' height='10' style='font-size:1px; line-height:1px;'>&nbsp;</td>
                                      </tr>
                                   </tbody>
                                </table>
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    <!-- End of seperator --> 
                    </td>
                    </tr>         
                    <tr>
                    <td width='100%'>
                    <!-- Start of footer -->
                    <table width='100%' bgcolor='#fcfcfc' cellpadding='0' cellspacing='0' border='0' id='backgroundTable' st-sortable='footer'>
                       <tbody>
                          <tr>
                             <td>
                                <table width='600' bgcolor='#eacb3c' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                   <tbody>
                                      <tr>
                                         <td width='100%'>
                                            <table bgcolor='#3b5999' width='600' cellpadding='0' cellspacing='0' border='0' align='center' class='devicewidth'>
                                               <tbody>
                                                  <!-- Spacing -->
                                                  <tr>
                                                     <td height='10' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
                                                  </tr>                                                  
                                                  <tr>
                                                     <td height='10' style='font-size:1px; line-height:1px; mso-line-height-rule: exactly;'>&nbsp;</td>
                                                  </tr>
                                                  <!-- Spacing -->
                                               </tbody>
                                            </table>
                                         </td>
                                      </tr>
                                   </tbody>
                                </table>
                             </td>
                          </tr>
                       </tbody>
                    </table>
                    <!-- End of footer -->
                             </td>
                          </tr>
                       </tbody>
                    </table>
                            </td>
                          </tr>
                       </tbody>
                    </table>         
                            </td>
                          </tr>
                       </tbody>
                    </table>
                       </body>
                       </html>
                       ";
                    $mail->AddAddress($email);
                    if(!$mail->Send()) 
                    {
                        echo "Failed";
                    }
                    else
                    {
                        echo "send";
                    } 
        ////////////////////////////////////////////////////////////////email///////////////////////////////////////////////////////////////////
        $url = $_SERVER['HTTP_REFERER'];
    }
    else
    {
    }
?>