# Hhcspheaders Test cases

> In order to check that a release is functional the following tests should be checked

## CSP


| Case Code            | Description                                    | Configuration                                                                                                                     | Result |  
|----------------------|------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------|--------|
| DISABLE_ALL          | Disable CSP everywhere                         | HHCSPHEADERS_ENABLE_FRONT = 0,HHCSPHEADERS_ENABLE_BACK = 0                                                                        | OK     |
| DISABLE_BO_ENABLE_FO | Disable in Back Office, enable in Front office | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 0,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_DEFAULT_SRC=localhost | OK     |
| DISABLE_FO_ENABLE_BO | Enable in Back Office, disable in Front office | HHCSPHEADERS_ENABLE_FRONT = 0,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_DEFAULT_SRC=localhost | OK     |
| CSP_MODE_REPORT_ONLY | Csp mode report only                           | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_DEFAULT_SRC=localhost | OK     |
| CSP_MODE_BLOCK_ONLY  | Csp mode block only                            | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = BLOCK,HHCSPHEADERS_CSP_DEFAULT_SRC=localhost       | OK     |
| CSP_MODE_BOTH        | Csp mode report and block                      | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = BOTH,HHCSPHEADERS_CSP_DEFAULT_SRC=localhost        | OK     |
| CSP_DEFAULT_SRC_ONLY | Only default src defined                       | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = BOTH,HHCSPHEADERS_CSP_DEFAULT_SRC=localhost        | OK     |
| CSP_ALL              | Define a value for all csp types               |                                                                                                                                   | OK     |
| CSP_SCRIPT_SRC       | Define a value for csp script                  | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_SCRIPT_SRC=localhost  | OK     |
| CSP_STYLE_SRC        | Define a value for csp style                   | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_STYLE_SRC=localhost   | OK     |
| CSP_IMG_SRC          | Define a value for csp img                     | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_IMG_SRC=localhost     | OK     |
| CSP_CONNECT_SRC      | Define a value for csp connect                 | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_CONNECT_SRC=localhost | OK     |
| CSP_FONT_SRC         | Define a value for csp font                    | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_FONT_SRC=localhost    | OK     |
| CSP_OBJECT_SRC       | Define a value for csp object                  | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_OBJECT_SRC=localhost  | OK     |
| CSP_MEDIA_SRC        | Define a value for csp media                   | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_MEDIA_SRC=localhost   | OK     |
| CSP_FRAME_SRC        | Define a value for csp frame                   | HHCSPHEADERS_ENABLE_FRONT = 1,HHCSPHEADERS_ENABLE_BACK = 1,HHCSPHEADERS_MODE = REPORT-ONLY,HHCSPHEADERS_CSP_FRAME_SRC=localhost   | OK     |


## REFERER POLICY

| Case Code                      | Description                  | Configuration                                                                                  | Result |  
|--------------------------------|------------------------------|------------------------------------------------------------------------------------------------|--------|
| REFERRER_DISABLE               | Referer disable              | HHCSPHEADERS_ENABLE_REFERRER = 0                                                               | OK     |
| REFERRER_NO_REFERRER           | Referer no referer           | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =no-referrer                     | OK     |
| REFERRER_NO_REFERRER_DOWNGRADE | Referer No referer downgrade | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =no-referrer-when-downgrade      | OK     |                      |                                        |                                     |        |
| REFERRER_ORIGIN                | Referer Origin               | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =origin                          | OK     |
| REFERRER_ORIGIN_CROSS          | Referer Origin cross         | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =origin-when-cross-origin        | OK     |
| REFERRER_ORIGIN_SAME           | Referer Origin Same          | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =same-origin                     | OK     |
| REFERRER_ORIGIN_STRICT         | Referer Origin strict        | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =strict-origin                   | OK     |
| REFERRER_ORIGIN_STRICT_CROSS   | Referer Origin Strict cross  | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =strict-origin-when-cross-origin | OK     |
| REFERRER_UNSAFE                | Referer Unsafe               | HHCSPHEADERS_ENABLE_REFERRER = 1,HHCSPHEADERS_REFERRER_POLICY =unsafe-url                      | OK     |

## XFRAME / XCONTENT

| Case Code                  | Description                            | Configuration                       | Result |  
|----------------------------|----------------------------------------|-------------------------------------|--------|
| FO_XCONTENT_XFRAME_DISABLE | Xframe content disabled                | HHCSPHEADERS_ENABLE_XCONTENT = 0    | OK     |
| FO_XCONTENT_XFRAME_ENABLE  | Xframe content enabled                 | HHCSPHEADERS_ENABLE_XCONTENT = 1    | OK     |