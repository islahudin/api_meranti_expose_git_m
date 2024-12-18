<?php

class HtmlTemplateHelper
{

  public function __construct()
  {
    //Create an instance; passing `true` enables exceptions
    date_default_timezone_set('Asia/Jakarta');
  }

  public function EmailTemplate(string $fullname, string $id_ticket, string $id_order)
  {

    $html = '
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>E-Ticket</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link
          href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
          rel="stylesheet"
        />
        <style type="text/css">
          @media only screen and (min-device-width: 601px) {
            .content {
              width: 600px !important;
            }
          }
          body {
            margin: 0;
            padding: 0;
            min-width: 100% !important;
            font-family: "Poppins", sans-serif;
          }
          .content {
            width: 100%;
            max-width: 600px;
          }
          hr {
            border-top: 1px solid #dadce0;
          }
        </style>
      </head>
      <body>
        <table
          border="0"
          cellpadding="0"
          cellspacing="0"
          width="100%"
          style="background-color: #f7f7f7; padding: 24px;"
        >
          <tr>
            <td>
              <!--[if (gte mso 9)|(IE)]>
              <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td>
                    <![endif]-->
              <table
                class="content"
                align="center"
                border="0"
                cellpadding="0"
                cellspacing="0"
                width="600"
                style="border-collapse: collapse; background-color: #ffffff;"
              >
                <tr>
                  <td style="padding: 24px;">
                    <img src="https://i.ibb.co/92cj7bb/logo-kreen.png" alt="" />
                  </td>
                </tr>
                <tr>
                  <td
                    style="
                      padding: 24px 0 0 24px;
                      font-size: 24px;
                      font-weight: 600;
                    "
                  >
                    Hi ' . $fullname . '
                  </td>
                </tr>
                <tr>
                  <td style="padding: 12px 24px;">
                    Thank you for ordering attraction tickets on Kreen. <br />
                    Your e-ticket has been published!
                  </td>
                </tr>
                <!-- qr code -->
                <tr>
                  <td style="padding: 24px; text-align: center;">
                    <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . $id_ticket . '&choe=UTF-8&chld=H" alt="" width="200px"/><br />
                    Ticket ID: ' . $id_ticket . '
                  </td>
                </tr>

                <!-- event details -->
                <tr>
                  <td
                    style="padding: 12px 24px; font-size: 24px; font-weight: 600;"
                  >
                    Event Details
                  </td>
                </tr>
                <tr>
                  <td
                    style="padding: 12px 24px; font-weight: 600; font-size: 18px;"
                  >
                    Ticket Details
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 24px;">
                    <table
                      style="
                        background-color: #f7f7f7;
                        width: 100%;
                        padding: 7px 12px;
                      "
                    >
                      <tr>
                        <td style="font-size: 14px; color: #7e7e7e;">
                          Order ID
                        </td>
                      </tr>
                      <tr>
                        <td>' . $id_order . '</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 24px;">
                    <table style="width: 100%; padding: 12px;">
                      <tr>
                        <td style="font-size: 14px; color: #7e7e7e;">
                          Event Name
                        </td>
                      </tr>
                      <tr>
                        <td>
                            Kuasai Integritas Diri
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 24px;">
                    <table
                      style="
                        background-color: #f7f7f7;
                        width: 100%;
                        padding: 7px 12px;
                      "
                    >
                      <tr>
                        <td style="font-size: 14px; color: #7e7e7e;">
                          Ticket Name
                        </td>
                      </tr>
                      <tr>
                        <td>GRATIISSSS</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 24px;">
                    <table style="width: 100%; padding: 12px;">
                      <tr>
                        <td style="font-size: 14px; color: #7e7e7e;">
                          Date and Time
                        </td>
                      </tr>
                      <tr>
                        <td>Saturday, 29-10-2022 19:00 - 21:00</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                    <td style="padding: 0 24px;">
                      <table style="width: 100%; padding: 12px;">
                        <tr>
                          <td style="font-size: 14px; color: #7e7e7e;">
                            Event Location
                          </td>
                        </tr>
                        <tr>
                          <td>Online Event</td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <tr>
                    <td style="padding: 0 24px;">
                      <table style="width: 100%; padding: 12px;">
                        <tr>
                          <td style="font-size: 14px; color: #7e7e7e;">
                            URL Link
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <a href="https://www.youtube.com/">
                              <button
                              style="
                                background: #e01a21;
                                border-radius: 10px;
                                font-weight: bold;
                                font-size: 18px;
                                padding: 12px 24px;
                                border: none;
                                color: #ffffff;
                                ">
                                Watch Here
                              </button>
                            </a>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <!-- guest details -->
                <tr>
                  <td
                    style="
                      padding: 24px 24px 12px 24px;
                      font-weight: 600;
                      font-size: 18px;
                    "
                  >
                    Guest Details
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 24px;">
                    <table
                      style="
                        background-color: #f7f7f7;
                        width: 100%;
                        padding: 7px 12px;
                      "
                    >
                      <tr>
                        <td>' . $fullname . '</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 24px;">
                    <table style="width: 100%; padding: 7px 12px;">
                      <tr>
                        <td>islahudin.soft01engineer@gmail.com</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0 24px 24px 24px;">
                    <table
                      style="
                        background-color: #f7f7f7;
                        width: 100%;
                        padding: 7px 12px;
                      "
                    >
                      <tr>
                        <td>085376791937</td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <!-- footer -->
                <tr>
                  <td style="padding: 24px; border-top: 1px solid #dadce0;">
                    <table>
                      <tr>
                        <td style="vertical-align: top;">
                          <table>
                            <tr>
                              <td style="padding-bottom: 24px;">
                                <img src="https://i.ibb.co/92cj7bb/logo-kreen.png" alt="" />
                              </td>
                            </tr>
                            <tr>
                              <td style="font-size: 12px;">Download Kreen App</td>
                            </tr>
                            <tr style="display:none;">
                              <td style="padding: 12px 0;">
                                <a href="#">
                                  <img src="https://i.ibb.co/2g7Bt1j/appstore-badge.png" alt=""
                                /></a>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <a href="https://play.google.com/store/apps/details?id=id.kreen.android.app">
                                  <img src="https://i.ibb.co/YTh7RQw/google-play-badge.png" alt=""
                                /></a>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td style="vertical-align: top;">
                          <table>
                            <tr>
                              <td
                                style="
                                  font-weight: bold;
                                  font-size: 18px;
                                  padding-bottom: 12px;
                                "
                              >
                                PT. Keren Entertainment Indonesia
                              </td>
                            </tr>
                            <tr>
                              <td style="font-size: 14px; padding-bottom: 29px;">
                                CoHive 101 9th Floor Suite 33, <br />
                                Kawasan Mega Kuningan Lot E4-7<br />
                                JL. DR. Ide Anak Agung Gde Agung No. 1 Kuningan
                                Timur, Setiabudi, Jakarta Selatan, DKI Jakarta,
                                12950
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <table style="width: 100%; padding-top: 8px;">
                                  <tr>
                                    <td style="font-weight: 600; font-size: 14px;">
                                      Follow Us
                                    </td>
                                    <td style="width: 1%; padding: 0 5px;">
                                      <a href="https://www.instagram.com/kreenindonesia/">
                                        <img src="https://i.ibb.co/F4H0Hst/Logo-Instagram.png" alt="" />
                                      </a>
                                    </td>
                                    <td style="width: 1%; padding: 0 5px;">
                                      <a href="https://web.facebook.com/kreenindonesia/?_rdc=1&_rdr">
                                        <img src="https://i.ibb.co/qRrGZzR/Logo-Facebook.png" alt="" />
                                      </a>
                                    </td>
                                    <td style="width: 1%; padding: 0 5px;">
                                      <a href="https://id.linkedin.com/company/kerenindonesia">
                                        <img src="https://i.ibb.co/QKNLqCH/Logo-Linked-In.png" alt="" />
                                      </a>
                                    </td>
                                    <td style="width: 1%; padding: 0 5px;">
                                      <a href="https://www.youtube.com/channel/UCCWAZWV7syzqYuZWSmE1N0g">
                                        <img src="https://i.ibb.co/Q88znJW/Logo-Youtube.png" alt="" />
                                      </a>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td
                    style="
                      text-align: center;
                      padding: 12px 0 13px;
                      font-weight: 600;
                      font-size: 14px;
                    "
                  >
                    Customer Services
                  </td>
                </tr>
                <tr>
                  <td align="center" style="padding-bottom: 24px;">
                    <table>
                      <tr>
                        <td style="padding: 0 7px;">
                          <table>
                            <tr>
                              <td><img src="https://i.ibb.co/wsWCNtF/whatsapp-black.png" width="20" alt=""></td>
                              <td>+62 857 1239 873</td>
                            </tr>
                          </table>
                        </td>
                        <td style="padding: 0 7px;">
                          <table>
                            <tr>
                              <td><img src="https://i.ibb.co/XJPs47B/email-black.png" width="20" alt=""></td>
                              <td>info@kreen.com</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <!--[if (gte mso 9)|(IE)]>
                      </td>
                  </tr>
              </table>
              <![endif]-->
            </td>
          </tr>
          <tr>
            <td style="text-align: center; padding: 24px;">
              © 2020 - ' . date("Y") . ' PT. Keren Entertainment Indonesia. <br />
              All Rights Reserved.
            </td>
          </tr>
        </table>
      </body>
    </html>

    ';

    return $html;
  }

  public function EmailPdf(string $fullname, string $id_ticket, string $id_order, string $type_event)
  {

    $html = '
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title>PDF Event Offline</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet"
      />
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
      <script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>

      <style type="text/css">
        @media only screen and (min-device-width: 601px) {
          .content {
            width: 600px !important;
          }
        }
        body {
          margin: 0;
          padding: 0;
          min-width: 100% !important;
          font-family: "Poppins", sans-serif;
        }
        .content {
          width: 100%;
          max-width: 600px;
        }
        hr {
          border-top: 1px solid #dadce0;
        }

        ol.alphabetic-list-low {
          list-style-type: lower-alpha;
        }
        ol.alphabetic-list-upper {
            list-style-type: upper-alpha;
        }
      </style>
    </head>
    <body>
      <table
        border="0"
        cellpadding="0"
        cellspacing="0"
        width="100%"
        style="background-color: #f7f7f7; padding: 24px;"
        >
        <tr>
          <td>
            <!--[if (gte mso 9)|(IE)]>
            <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <![endif]-->
            <table
              class="content"
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              width="600"
              style="border-collapse: collapse; background-color: #ffffff;"
            >
              <tr>
                <td>
                  <table style="width: 100%; padding: 0 24px;">
                    <tr>
                      <td>
                        <table>
                          <tr>
                            <td style="color: #a9a9a9;">
                              <span
                                class="iconify"
                                data-icon="gg:trees"
                                data-inline="false"
                                style="
                                  color: white;
                                  background-color: #e01a21;
                                  font-size: 25px;
                                  padding: 10px;
                                  margin-right: 10px;
                                  border-radius: 10px;
                                "
                              ></span>
                            </td>
                            <td style="text-align: left;">
                              <div style="font-weight: bold;">
                                ' . ucfirst($type_event) . ' Event E-Tickets
                              </div>
                              <div style="font-weight: 500; font-size: 12px;">
                                Order ID: ' . $id_order . '
                              </div>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td style="text-align: right;">
                        <img
                          style="height: 30px; width: auto; margin-left: 24px;"
                          src="https://i.ibb.co/92cj7bb/logo-kreen.png"
                          alt=""
                        />

                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <!-- detail event -->
              <tr>
                <td style="padding: 0 24px;">
                  <table>
                    <tr>
                      <td>
                        <img
                          style="width: 130px; height: auto;"
                          src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . $id_ticket . '&choe=UTF-8&chld=H"
                          alt=""
                        />
                      </td>
                      <td style="vertical-align: top; padding: 0 0 0 24px;">
                        <table>
                          <tr>
                            <td>
                              <table>
                                <tr>
                                  <td
                                    style="
                                      font-weight: 600;
                                      padding-bottom: 12px;
                                    "
                                  >
                                  Kuasai Integritas Diri
                                  </td>
                                </tr>
                                <tr>
                                  <td style="font-weight: 600; font-size: 14px;">
                                  GRATIISSSS
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <table style="font-size: 12px;">
                                      <tr>
                                        <td>
                                          <span
                                            class="iconify"
                                            data-icon="carbon:location"
                                            data-inline="false"
                                            style="margin-right: 15px;"
                                          ></span>
                                        </td>
                                        <td>Online Event via Zoom</td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <table style="font-size: 12px;">
                                      <tr>
                                        <td>
                                          <span
                                            class="iconify"
                                            data-icon="uil:calender"
                                            data-inline="false"
                                            style="margin-right: 15px;"
                                          ></span>
                                        </td>
                                        <td>Saturday, 29-10-2022</td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td>
                                    <table style="font-size: 12px;">
                                      <tr>
                                        <td>
                                          <span
                                            class="iconify"
                                            data-icon="ic:baseline-access-time"
                                            data-inline="false"
                                            style="margin-right: 15px;"
                                          ></span>
                                        </td>
                                        <td>19:00 - 21:00</td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td>
                  <table style="width: 100%;">
                    <tr>
                      <td>
                        <table style="width: 110%;">
                          <tr>
                            <td
                              style="
                                padding: 0 0 12px 24px;
                                font-weight: 600;
                                font-size: 18px;
                              "
                            >
                              Participant Details
                            </td>
                          </tr>
                          <tr>
                            <td style="padding: 0 24px; font-size: 12px;">
                              <table
                                style="
                                  background-color: #f7f7f7;
                                  width: 100%;
                                  padding: 4px 12px;
                                "
                              >
                                <tr>
                                  <td style="width: 45%;">Name</td>
                                  <td>' . $fullname . '</td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td style="padding: 0 24px; font-size: 12px;">
                              <table style="width: 100%; padding: 4px 12px;">
                                <tr>
                                  <td style="width: 45%;">Email</td>
                                  <td>
                                    islahudin.soft01engineer@gmail.com
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td style="padding: 0 24px; font-size: 12px;">
                              <table
                                style="
                                  background-color: #f7f7f7;
                                  width: 100%;
                                  padding: 4px 12px;
                                "
                              >
                                <tr>
                                  <td style="width: 45%;">Phone Number</td>
                                  <td>
                                    085376791937
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td style="padding: 0 24px; font-size: 12px;">
                              <table style="width: 100%; padding: 4px 12px;">
                                <tr>
                                  <td style="width: 45%;">Valid Date</td>
                                  <td>
                                    Valid for Saturday, 29-10-2022
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td style="padding: 0 24px; font-size: 12px;">
                              <table
                                style="
                                  background-color: #f7f7f7;
                                  width: 100%;
                                  padding: 4px 12px;
                                "
                              >
                                <tr>
                                  <td style="width: 45%;">Status</td>
                                  <td
                                    style="

                                      color: #219653;

                                    "
                                  >

                              <!--
                                    <span
                                      style="margin-right: 3px;"
                                      class="iconify"
                                      data-icon="akar-icons:circle-check"
                                      data-inline="false"
                                    ></span>
                                    -->
                                    Claimed
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>';

    if ($type_event == "online") {
      # code...
      $html .= '
                            <tr>
                              <td style="padding: 0 24px; font-size: 12px;">
                                <table style="width: 100%; padding: 4px 12px;">
                                  <tr>
                                    <td style="width: 45%;">Event Link</td>
                                    <td>
                                      https://www.youtube.com/
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            ';
    }

    $html .= '

                        </table>
                      </td>
                      <td
                        style="
                          vertical-align: middle;
                          text-align: center;
                          font-size: 14px;
                        "
                      >
                        <img width="150px" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . $id_ticket . '&choe=UTF-8&chld=H" alt="" />
                        <div>Ticket ID</div>
                        <div style="font-weight: 600;">' . $id_ticket . '</div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              ';
    if ($type_event == "online") {
      # code...
      $html .= '
                <tr>
                  <td style="padding: 0 24px;">
                    <table>
                      <tr>
                        <td style="font-weight: 600; padding: 8px 0;">
                          How to Join
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size: 12px;">
                          You can find the event link via the Kreen.id website, Kreen App, and your email for online events. <br />
                          <ol class="alphabetic-list-upper" style="margin: 0; padding: 0 1.3em; font-size: 12px;">
                              <li >How to join via the Kreen.id website</li>
                                <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                  <li>Open the Kreen.id website</li>
                                  <li>Log in to your Kreen account</li>
                                  <li>Go to the dashboard, then select the My Ticket menu</li>
                                  <li>Choose your ticket and click the E-Ticket</li>
                                  <li>Click the Watch Here button</li>
                                </ul>
                              <li>How to join via the Kreen App</li>
                                <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                  <li>Open the Kreen App</li>
                                  <li>Log in to your Kreen account</li>
                                  <li>Go to the My Ticket menu</li>
                                  <li>Select your ticket</li>
                                  <li>Click the Watch Here button</li>
                                </ul>
                              <li>How to join via email</li>
                                <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                  <li>Open your email</li>
                                  <li>Find the e-ticket from Kreen</li>
                                  <li>Click the watch here button</li>
                                </ul>
                          </ol>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                ';
    } else {
      $html .= '
                <tr>
                  <td style="padding: 0 24px;">
                    <table>
                      <tr>
                        <td style="font-weight: 600; padding: 8px 0;">
                          How to Join
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size: 12px;">
                          <ol type="A" style="margin: 0; padding: 0 1.3em; font-size: 12px;">
                              <li >Venue Entry Procedure</li>
                                <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                  <li>Visitors enter the venue through the entrance access that has been determined by the event organizer</li>
                                  <li>Visitors maintain a safe distance of at least 1 (one) meter by following the queue line</li>
                                  <li>Take body temperature measurements at the access/entrance for all visitors</li>
                                  <li>If it is found that the temperature is> 37.3 C (2 checks with a distance of 5 minutes), it is not allowed to enter</li>
                                  <li>Each visitor must show an entry sign (ticket/barcode / ID Card)</li>
                                    <ol type="A" style="margin: 0; padding: 0 1.3em; font-size: 12px;">
                                        <li >One entry is only valid for one person;</li>
                                        <li >The name listed must match the real name by showing a valid ID</li>
                                    </ol>
                                  <li>Visitors who will enter the venue must wear Personal Protective Equipment (PPE) that the event organizer has determined</li>
                                  <li>Visitors who will enter the performance area must wash their hands with soap and running water or use a hand sanitiser</li>
                                </ul>
                              <li>Venue Exit Procedure</li>
                                <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                  <li>Visitors exit through the exit access that has been determined by the organizer of the activity (event)</li>
                                  <li>In leaving, efforts were made to prevent mass build-up and minimize physical contact or use technical engineering to avoid crowds</li>
                                  <li>If visitors leave simultaneously, the first to exit is the visitor closest to the exit or perform technical engineering to avoid physical contact and crowds</li>
                                  <li>Follow the directions from the staff who arrange the exit process from the venue (venue)</li>
                                </ul>
                          </ol>
                          <p style="margin: 0; padding: 0 0 8px 0; font-size: 12px;">
                          This procedure is based on Panduan Pelaksanaan Kebersihan, Kesehatan, Keselamatan, dan Kelestarian Lingkungan di Penyelenggaraan Kegiatan (Event) (Kemenparekraf/Baparekraf) Edisi September 2020. Source: https://bit.ly/3v9ABHt</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                ';
    }

    $html .= '
              <tr>
                <td style="padding: 0 24px;">
                  <table>
                    <tr>
                      <td style="font-weight: 600; padding: 8px 0;">
                        How to Get Certificate
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p style="margin: 0; padding: 0 0 8px 0; font-size: 12px;">If the event organizer provides certificates for participants, you can get certificates through the Kreen.id website, the Kreen application, and your email.</p>
                        <ol style="margin: 0; padding: 0 1.3em; font-size: 12px; list-style-type: lower-alpha;">
                            <li >How to get a certificate through the Kreen.id website</li>
                              <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                <li>Open the Kreen.id site</li>
                                <li>Log in using your account</li>
                                <li>Open the dashboard, then select the menu my certificates</li>
                                <li>On the Active tab, choose your event and click get a certificate</li>
                              </ul>
                            <li>How to get a certificate through the Kreen application</li>
                              <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                <li>Open the Kreen Application</li>
                                <li>Log in to your Kreen account</li>
                                <li>Select the My Profile menu, then click My Certificate</li>
                                <li>In the Active tab, find the certificate you want to get and click claim certificate.</li>
                              </ul>
                            <li>How do I get a certificate by email</li>
                              <ul style="margin: 0 0 10px 0; padding: 0 1.3em; font-size: 12px;">
                                <li>After the event is finished, you will get an email to get a certificate</li>
                                <li>Open your email</li>
                                <li>Look for an email related to the event certificate from Kreen</li>
                                <li>Click the get certificate button</li>
                              </ul>
                        </ol>
                        <p style="margin: 0; padding: 0 0 8px 0; font-size: 12px;">Note:</p>
                        <ol style="margin: 0 0 10px 0; padding: 0 2.5em; font-size: 12px;">
                          <li>If you need an OTP code, ask the Event Organizer who organized the event</li>
                          <li>If the certificate doesn&#39;t require an OTP code, you can get the certificate right away</li>
                          <li>You may be asked to fill out a feedback form first before getting a certificate</li>
                        </ol>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding: 0 24px;">
                  <table>
                    <tr>
                      <td style="font-weight: 600; padding: 8px 0;">
                        Event Rules
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <ol style="margin: 0; padding: 0 1.3em; font-size: 12px;">
                          <li>
                            Peserta diwajibkan untuk menggunakan Username sesuai
                            dengan nama yang didaftarkan
                          </li>
                          <li>
                            Peserta di anjurkan menggunakan alamat email yang
                            didaftarkan untuk masuk ke dalam Zoom
                          </li>
                        </ol>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding: 0 24px 24px;">
                  <table>
                    <tr>
                      <td style="font-weight: 600; padding: 8px 0;">
                        Organizer Contact Information
                      </td>
                    </tr>
                    <tr>
                      <td
                        style="
                          display: flex;
                          align-items: center;
                          font-size: 12px;
                        "
                      >
                        <span
                          style="padding-right: 8px;"
                          class="iconify"
                          data-icon="carbon:phone"
                          data-inline="false"
                        ></span>
                        <div>085959776736</div>
                      </td>
                    </tr>
                    <tr>
                      <td
                        style="
                          display: flex;
                          align-items: center;
                          font-size: 12px;
                        "
                      >
                        <span
                          style="padding-right: 8px;"
                          class="iconify"
                          data-icon="ant-design:mail-outlined"
                          data-inline="false"
                        ></span>
                        <div>wahyu.andhik@gmail.com</div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <!-- footer -->
              <tr>
                <td style="padding: 24px; border-top: 10px solid #e01a21;">
                  <table>
                    <tr>
                      <td style="width: 50%;">
                        <table>
                          <tr>
                            <td style="font-weight: 600; font-size: 14px;">
                              PT. Keren Entertainment Indonesia
                            </td>
                          </tr>
                          <tr>
                            <td style="font-size: 9px;">
                              Jakarta CoHive 101 9th Floor, Kawasan Mega Kuningan
                              Lot E4-7, JL. DR. Ide Anak Agung Gde Agung No. 1
                              Kuningan Timur, Setiabudi, Jakarta Selatan, DKI
                              Jakarta 12950
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td style="vertical-align: baseline;">
                        <table>
                          <tr>
                            <td style="font-weight: 600; font-size: 14px;">
                              Customer Service
                            </td>
                          </tr>
                          <tr>
                            <td
                              style="
                                display: inline-flex;
                                align-items: center;
                                font-size: 9px;
                                margin-right: 4px;
                              "
                            >
                              <span
                                style="padding-right: 4px;"
                                class="iconify"
                                data-icon="eva:phone-call-outline"
                                data-inline="false"
                              ></span>
                              <div>0812 8128 8121</div>
                            </td>
                            <td
                              style="
                                display: inline-flex;
                                align-items: center;
                                font-size: 9px;
                                margin-right: 4px;
                              "
                            >
                              <span
                                style="padding-right: 4px;"
                                class="iconify"
                                data-icon="akar-icons:whatsapp-fill"
                                data-inline="false"
                              ></span>
                              <div>+62 857 1239 873</div>
                            </td>
                            <td
                              style="
                                display: inline-flex;
                                align-items: center;
                                font-size: 9px;
                                margin-right: 4px;
                              "
                            >
                              <span
                                style="padding-right: 4px;"
                                class="iconify"
                                data-icon="ant-design:mail-outlined"
                                data-inline="false"
                              ></span>
                              <div>info@kreen.com</div>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
                    </td>
                </tr>
            </table>
            <![endif]-->
          </td>
        </tr>
      </table>
      <script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>
    </body>
  </html>

  ';

    return $html;
  }
}
