<?php

namespace App\Controllers;

use App\Models\GuestEntry;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
// use DbConnect;
use PDO;
use DbHandler;
use MailerFunction;
use Mpdf\Mpdf as _mpdf;



class GeneratePdfController
{

  protected  $customResponse;
  protected  $validator;

  protected  $mpdf;

  public function  __construct()
  {
    $this->customResponse = new CustomResponse();
    $this->validator = new Validator();

    $this->mpdf = new _mpdf;

    date_default_timezone_set('Asia/Jakarta');
  }

  public function generatePdfTest(Request $request, Response $response)
  {

    $html = '
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f7f7f7;padding:24px">
          <tbody><tr>
            <td>
              
              <table class="m_-7924357823817654179content" align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;background-color:#ffffff">
                <tbody><tr>
                  <td style="padding:24px">
                    <img src="https://ci4.googleusercontent.com/proxy/G7PTxRP3a_yholg-LtceGGgx3AiOXwE-z7S-DACZ65FnGhjCXOYECvYPSZhP9IRl3sM4tp1LX01q9g=s0-d-e1-ft#https://i.ibb.co/92cj7bb/logo-kreen.png" alt="" class="CToWUd" data-bit="iit">
                  </td>
                </tr>
                <tr>
                  <td style="padding:24px 0 0 24px;font-size:24px;font-weight:600">
                    Hi islah021 soft021
                  </td>
                </tr>
                <tr>
                  <td style="padding:12px 24px">
                    Thank you for ordering attraction tickets on Kreen. <br>
                    Your e-ticket has been published!
                  </td>
                </tr>
                
                <tr>
                  <td style="padding:24px;text-align:center">
                    
                    
                    <img src="https://ci5.googleusercontent.com/proxy/G3nb1LU_petlxgdksMsCbmDP3LpV7C1dtD_waZ2oz-qtmWbbp5ynJlWb5PrK8IOdr7CYYZ9yi0r2kvOFa_Z1XRzk-lGe-ujmk1ib9hIi8Q0k9aBd6qxtU3yNHfgqza0LmmFkH-1rh5I=s0-d-e1-ft#https://chart.googleapis.com/chart?chs=200x200&amp;cht=qr&amp;chl=952913675&amp;choe=UTF-8&amp;chld=H" alt="" width="200px" class="CToWUd" data-bit="iit"><br>
                    Ticket ID: 952913675
                  </td>
                </tr>

                
                <tr>
                  <td style="padding:12px 24px;font-size:24px;font-weight:600">
                    Event Details
                  </td>
                </tr>
                <tr>
                  <td style="padding:12px 24px;font-weight:600;font-size:18px">
                    Ticket Details
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 24px">
                    <table style="background-color:#f7f7f7;width:100%;padding:7px 12px">
                      <tbody><tr>
                        <td style="font-size:14px;color:#7e7e7e">
                          Order ID
                        </td>
                      </tr>
                      <tr>
                        <td>132843574</td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 24px">
                    <table style="width:100%;padding:12px">
                      <tbody><tr>
                        <td style="font-size:14px;color:#7e7e7e">
                          Event Name
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Kuasai Integritas Diri
                        </td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 24px">
                    <table style="background-color:#f7f7f7;width:100%;padding:7px 12px">
                      <tbody><tr>
                        <td style="font-size:14px;color:#7e7e7e">
                          Ticket Name
                        </td>
                      </tr>
                      <tr>
                        <td>GRATIISSSS</td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 24px">
                    <table style="width:100%;padding:12px">
                      <tbody><tr>
                        <td style="font-size:14px;color:#7e7e7e">
                          Date and Time
                        </td>
                      </tr>
                      <tr>
                        <td>Saturday, 29-10-2022
                        19:00 -
                        21:00</td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                
                  <tr>
                    <td style="padding:0 24px">
                      <table style="width:100%;padding:12px">
                        <tbody><tr>
                          <td style="font-size:14px;color:#7e7e7e">
                            Event Location
                          </td>
                        </tr>
                        <tr>
                          <td>Online Event</td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>
                  
                  <tr>
                    <td style="padding:0 24px">
                      <table style="width:100%;padding:12px">
                        <tbody><tr>
                          <td style="font-size:14px;color:#7e7e7e">
                            URL Link
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <a href="http://test.com" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://test.com&amp;source=gmail&amp;ust=1667154988627000&amp;usg=AOvVaw0HN2t3eCa8W4iRQKAeYhFC">
                              <button style="background:#e01a21;border-radius:10px;font-weight:bold;font-size:18px;padding:12px 24px;border:none;color:#ffffff">
                                Watch Here
                              </button>
                            </a>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>
                  
                
                <tr>
                  <td style="padding:24px 24px 12px 24px;font-weight:600;font-size:18px">
                    Guest Details
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 24px">
                    <table style="background-color:#f7f7f7;width:100%;padding:7px 12px">
                      <tbody><tr>
                        <td>islah021 soft021</td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 24px">
                    <table style="width:100%;padding:7px 12px">
                      <tbody><tr>
                        <td><a href="mailto:islahudin.soft01engineer@gmail.com" target="_blank">islahudin.soft01engineer@<wbr>gmail.com</a></td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:0 24px 24px 24px">
                    <table style="background-color:#f7f7f7;width:100%;padding:7px 12px">
                      <tbody><tr>
                        <td>085376791937</td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                
                <tr>
                  <td style="padding:24px;border-top:1px solid #dadce0">
                    <table>
                      <tbody><tr>
                        <td style="vertical-align:top">
                          <table>
                            <tbody><tr>
                              <td style="padding-bottom:24px">
                                <img src="https://ci4.googleusercontent.com/proxy/G7PTxRP3a_yholg-LtceGGgx3AiOXwE-z7S-DACZ65FnGhjCXOYECvYPSZhP9IRl3sM4tp1LX01q9g=s0-d-e1-ft#https://i.ibb.co/92cj7bb/logo-kreen.png" alt="" class="CToWUd" data-bit="iit">
                              </td>
                            </tr>
                            <tr>
                              <td style="font-size:12px">Download Kreen App</td>
                            </tr>
                            <tr style="display:none">
                              <td style="padding:12px 0">
                                <a href="#m_-7924357823817654179_">
                                  <img src="https://ci4.googleusercontent.com/proxy/O92BTDVIMao3P3x9dkIJfTmZo90e5Wqtd2lygIOUpMVzIfroxheRIvfgcpdpnOwVlE4cz5QZOGP_AWMgjlM=s0-d-e1-ft#https://i.ibb.co/2g7Bt1j/appstore-badge.png" alt="" class="CToWUd" data-bit="iit"></a>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <a href="https://play.google.com/store/apps/details?id=id.kreen.android.app" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://play.google.com/store/apps/details?id%3Did.kreen.android.app&amp;source=gmail&amp;ust=1667154988627000&amp;usg=AOvVaw3avwDK6ErKPoHl1hkM86rq">
                                  <img src="https://ci6.googleusercontent.com/proxy/4-24BmYlMVHPfCbLl0AKJC6PJqzv_jHhgAgChIh8rcpySw5_6ROFSZYFeMotVGGcMyIoBs3Vh5UVoAXt2WqzP18=s0-d-e1-ft#https://i.ibb.co/YTh7RQw/google-play-badge.png" alt="" class="CToWUd" data-bit="iit"></a>
                              </td>
                            </tr>
                          </tbody></table>
                        </td>
                        <td style="vertical-align:top">
                          <table>
                            <tbody><tr>
                              <td style="font-weight:bold;font-size:18px;padding-bottom:12px">
                                PT. Keren Entertainment Indonesia
                              </td>
                            </tr>
                            <tr>
                              <td style="font-size:14px;padding-bottom:29px">
                                CoHive 101 9th Floor Suite 33, <br>
                                Kawasan Mega Kuningan Lot E4-7<br>
                                JL. DR. Ide Anak Agung Gde Agung No. 1 Kuningan
                                Timur, Setiabudi, Jakarta Selatan, DKI Jakarta,
                                12950
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <table style="width:100%;padding-top:8px">
                                  <tbody><tr>
                                    <td style="font-weight:600;font-size:14px">
                                      Follow Us
                                    </td>
                                    <td style="width:1%;padding:0 5px">
                                      <a href="https://www.instagram.com/kreenindonesia/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.instagram.com/kreenindonesia/&amp;source=gmail&amp;ust=1667154988627000&amp;usg=AOvVaw1v8QgfCMoATOl0PMQd46ze">
                                        <img src="https://ci5.googleusercontent.com/proxy/hoh3IXuGoKPxNKiwuxj9BHX9ZWDvgmThn9-3bUODnHfJezC5fs48wJjbCvmIOuBrvbMAd-vZKsRJniVo8J4=s0-d-e1-ft#https://i.ibb.co/F4H0Hst/Logo-Instagram.png" alt="" class="CToWUd" data-bit="iit">
                                      </a>
                                    </td>
                                    <td style="width:1%;padding:0 5px">
                                      <a href="https://web.facebook.com/kreenindonesia/?_rdc=1&amp;_rdr" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://web.facebook.com/kreenindonesia/?_rdc%3D1%26_rdr&amp;source=gmail&amp;ust=1667154988627000&amp;usg=AOvVaw3D9YhlM42tt-ZQmI7Z6Z2V">
                                        <img src="https://ci4.googleusercontent.com/proxy/0JFp293_YtUYXPT-M0hHPpbtoyi9rXDzo4nI3_AqWi_f-6_k201jUbG68rQYuMjdmNZq7tFJH4OKw2hbyA=s0-d-e1-ft#https://i.ibb.co/qRrGZzR/Logo-Facebook.png" alt="" class="CToWUd" data-bit="iit">
                                      </a>
                                    </td>
                                    <td style="width:1%;padding:0 5px">
                                      <a href="https://id.linkedin.com/company/kerenindonesia" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://id.linkedin.com/company/kerenindonesia&amp;source=gmail&amp;ust=1667154988628000&amp;usg=AOvVaw1xIMEMWdvWSsVwtJMGnfCh">
                                        <img src="https://ci4.googleusercontent.com/proxy/Jej_JQUPwAPEsjgvij97UpuPOXBMyzJM6xuaeSLVNun0CsENz0cb_yrzscc8oqj6G3yVGMvO_7Oe_8kTbn4=s0-d-e1-ft#https://i.ibb.co/QKNLqCH/Logo-Linked-In.png" alt="" class="CToWUd" data-bit="iit">
                                      </a>
                                    </td>
                                    <td style="width:1%;padding:0 5px">
                                      <a href="https://www.youtube.com/channel/UCCWAZWV7syzqYuZWSmE1N0g" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.youtube.com/channel/UCCWAZWV7syzqYuZWSmE1N0g&amp;source=gmail&amp;ust=1667154988628000&amp;usg=AOvVaw3JMnzlz5vJLAB-NmbTHz4n">
                                        <img src="https://ci3.googleusercontent.com/proxy/7HEu5ZhGputQjA5q63oSg9SbZc345saQ1jaIyBjOGXXrBJHbxlOpdH4GZODBQgod-2fN-lAgt42UjjOY=s0-d-e1-ft#https://i.ibb.co/Q88znJW/Logo-Youtube.png" alt="" class="CToWUd" data-bit="iit">
                                      </a>
                                    </td>
                                  </tr>
                                </tbody></table>
                              </td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
                <tr>
                  <td style="text-align:center;padding:12px 0 13px;font-weight:600;font-size:14px">
                    Customer Services
                  </td>
                </tr>
                <tr>
                  <td align="center" style="padding-bottom:24px">
                    <table>
                      <tbody><tr>
                        <td style="padding:0 7px">
                          <table>
                            <tbody><tr>
                              <td><img src="https://ci3.googleusercontent.com/proxy/Flu6CLQYNL4XZrsqvTYLLenq1MMDVbH3KQ_wFWYO4WSJx2bBUg2xgSZZuWhwRHw7PmyPkAJpIPew7YAO7ss=s0-d-e1-ft#https://i.ibb.co/wsWCNtF/whatsapp-black.png" width="20" alt="" class="CToWUd" data-bit="iit"></td>
                              <td>+62 857 1239 873</td>
                            </tr>
                          </tbody></table>
                        </td>
                        <td style="padding:0 7px">
                          <table>
                            <tbody><tr>
                              <td><img src="https://ci6.googleusercontent.com/proxy/JgKbqHsTFINvcjcXgVQvlkX9YjBz8--BWK-UuoaIH3nFO9DuKhLSd2VxbXuUMxHoDk7KP1tsz9ASrIU=s0-d-e1-ft#https://i.ibb.co/XJPs47B/email-black.png" width="20" alt="" class="CToWUd" data-bit="iit"></td>
                              <td><a href="mailto:info@kreen.com" target="_blank">info@kreen.com</a></td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
              </tbody></table>
              
            </td>
          </tr>
          <tr>
            <td style="text-align:center;padding:24px">
              © 2020 - 2022 PT. Keren Entertainment Indonesia. <br>
              All Rights Reserved.
            </td>
          </tr>
        </tbody></table>
        ';

    // echo $html;

    $this->mpdf->WriteHTML($html);
    $this->mpdf->Output();
  }
}
