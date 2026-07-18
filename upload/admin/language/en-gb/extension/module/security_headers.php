<?php
// Heading
$_['heading_title']                             = 'Opencart Security Headers';
// Text
$_['text_module']                               = 'Modules';
$_['text_success']                              = 'Success: You have modified Opencart Security Headers!';
$_['text_edit']                                 = 'Edit your security headers settings';
$_['text_default']                              = 'Default Store';
$_['text_select_store']                         = 'Select Store';
$_['text_stores']                               = 'Stores: ';
$_['text_insecure']                             = 'Insecure';
// Placeholder
$_['placeholder_expect_ct_report_uri']          = 'Type report url address..';
$_['placeholder_expect_ct_max_age']             = 'Type max age in seconds..';
$_['placeholder_strict_transport_security']     = 'Type max age in seconds..';
// Entry
$_['entry_status']                              = 'Extension Status';
$_['entry_X_Powered_By']                        = 'X-Powered-By';
$_['entry_X_HTTP_Method_Override']              = 'X-HTTP-Method-Override';
$_['entry_proxy']                               = 'HTTP Proxy';
$_['entry_forward']                             = 'HTTP Forwards';
$_['entry_ranges']                              = 'HTTP Ranges';
$_['entry_X_XSS_Protection']                    = 'X-XSS-Protection';
$_['entry_X_Frame_Options']                     = 'X-Frame-Options';
$_['entry_X_Content_Type_Options']              = 'X-Content-Type-Options';
$_['entry_Referrer_Policy']                     = 'Referrer-Policy';
$_['entry_Content_Security_Policy']             = 'Content-Security-Policy';
$_['entry_max_age']                             = 'Max-Age';

// About
$_['about_extension']                           = 'HTTP headers let the client and the server pass additional information with an HTTP request or response. An HTTP header consists of its case-insensitive name followed by a colon (:), then by its value. Whitespace before the value is ignored.<br/><br/>There are a lot of things to consider to when securing your website or web application, but a good place to start is to explore your HTTP security headers and ensure you are keeping up with best practices. In many cases they are very easy to implement and only require a slight web server configuration change. HTTP security headers provide yet another layer of security by helping to mitigate attacks and security vulnerabilities.<br/></br/>Whenever a browser requests a page from a web server, the server responds with the content along with HTTP response headers. Some of these headers contain content meta data such as the content-encoding, cache-control, status error codes, etc.<br/>
<br/>
Along with these are also HTTP security headers that tell your browser how to behave when handling your website’s content. For example, by using the strict-transport-security you can force the browser to communicate solely over HTTPS. There are six different HTTP security headers that we will explore below (in no particular order) that you should be aware of and we recommend implementing if possible.';

$_['about_X_Powered_By']                        = 'May be set by hosting environments or other frameworks and contains information about them while not providing any usefulness to the application or its visitors. Unset this header to avoid exposing potential vulnerabilities.<br/><br/><strong>Recommendation: </strong>Disabled';

$_['about_X_XSS_Protection']                    = 'The x-xss-protection header is designed to enable the cross-site scripting (XSS) filter built into modern web browsers. This is usually enabled by default, but using it will enforce it. It is supported by Internet Explorer 8+, Chrome, and Safari.<br/>The HTTP X-XSS-Protection response header is a feature of Internet Explorer, Chrome and Safari that stops pages from loading when they detect reflected cross-site scripting (XSS) attacks. Although these protections are largely unnecessary in modern browsers when sites implement a strong Content-Security-Policy that disables the use of inline JavaScript (\'unsafe-inline\'), they can still provide protections for users of older web browsers that don\'t yet support CSP.<br/><strong>Recommendation: </strong>1; mode=block';

$_['about_X_Frame_Options']                     = 'The x-frame-options header provides clickjacking protection by not allowing iframes to load on your website. It is supported by IE 8+, Chrome 4.1+, Firefox 3.6.9+, Opera 10.5+, Safari 4+.<br/>The <strong>X-Frame-Options</strong> HTTP response header can be used to indicate whether or not a browser should be allowed to render a page in a <i>frame</i>, <i>iframe</i>, <i>embed</i> or <i>object</i>. Sites can use this to avoid clickjacking attacks, by ensuring that their content is not embedded into other sites.<br/>
<br/>
The added security is only provided if the user accessing the document is using a browser supporting X-Frame-Options.<br/><strong>Recommendation: </strong>Same Origin';

$_['about_X_Content_Type_Options']              = 'The x-content-type-options header prevents Internet Explorer and Google Chrome from sniffing a response away from the declared content-type. This helps reduce the danger of drive-by downloads and helps treat the content the right way.<br/>The <strong>X-Content-Type-Options</strong> response HTTP header is a marker used by the server to indicate that the MIME types advertised in the Content-Type headers should not be changed and be followed. This allows to opt-out of MIME type sniffing, or, in other words, it is a way to say that the webmasters knew what they were doing.<br/>
<br/>
This header was introduced by Microsoft in IE 8 as a way for webmasters to block content sniffing that was happening and could transform non-executable MIME types into executable MIME types. Since then, other browsers have introduced it, even if their MIME sniffing algorithms were less aggressive.<br/>
<br/>
Site security testers usually expect this header to be set.<br/><strong>Recommendation: </strong>No Sniff';

$_['about_Referrer_Policy']                     = 'When a user clicks a link on one site, the origin, that takes them to another site, the destination, the destination site receives information about the origin the user came from. This is how we get metrics like those provided by Google Analytics on where our traffic came from. I know that 4,000 users came from Twitter this week because when they visit my site they set the referer[sic] header in their request.<br/><br/>
<strong>Directives</strong>
<br/><br/>
<strong>no-referrer</strong>
<p>The Referer header will be omitted entirely. No referrer information is sent along with requests.</p>
<strong>no-referrer-when-downgrade (default)</strong>
<p>This is the default behavior if no policy is specified, or if the provided value is invalid. The origin, path, and querystring of the URL are sent as a referrer when the protocol security level stays the same (HTTP→HTTP, HTTPS→HTTPS) or improves (HTTP→HTTPS), but isn\'t sent to less secure destinations (HTTPS→HTTP).</p>
<strong>origin</strong>
<p>Only send the origin of the document as the referrer.</p>
<p>For example, a document at https://example.com/page.html will send the referrer https://example.com/.</p>
<strong>origin-when-cross-origin</strong>
<p>Send the origin, path, and query string when performing a same-origin request, but only send the origin of the document for other cases.</p>
<strong>same-origin</strong>
<p>A referrer will be sent for same-site origins, but cross-origin requests will send no referrer information.</p>
<strong>strict-origin</strong>
<p>Only send the origin of the document as the referrer when the protocol security level stays the same (HTTPS→HTTPS), but don\'t send it to a less secure destination (HTTPS→HTTP).</p>
<strong>strict-origin-when-cross-origin</strong>
<p>Send the origin, path, and querystring when performing a same-origin request, only send the origin when the protocol security level stays the same (HTTPS→HTTPS), and send no header to a less secure destination (HTTPS→HTTP).</p>
<strong>unsafe-url</strong>
<p>Send the origin, path, and query string when performing any request, regardless of security.</p>
<strong>Recommendation: </strong>Strict When Cross Origin';

$_['about_Strict_Transport_Security']           = 'The <strong>Strict Transport Security</strong> header is a security enhancement that restricts web browsers to access web servers solely over HTTPS. This ensures the connection cannot be establish through an insecure HTTP connection which could be susceptible to attacks.<br/><br/><strong>An example scenario</strong><br/>
<br/>
You log into a free WiFi access point at an airport and start surfing the web, visiting your online banking service to check your balance and pay a couple of bills. Unfortunately, the access point you\'re using is actually a hacker\'s laptop, and they\'re intercepting your original HTTP request and redirecting you to a clone of your bank\'s site instead of the real thing. Now your private data is exposed to the hacker.<br/><br/>
<strong>Strict Transport Security</strong> resolves this problem; as long as you\'ve accessed your bank\'s web site once using HTTPS, and the bank\'s web site uses <strong>Strict Transport Security</strong>, your browser will know to automatically use only HTTPS, which prevents hackers from performing this sort of man-in-the-middle attack.<br/><br/><strong>How the browser handles it</strong>
<br/>
The first time your site is accessed using HTTPS and it returns the <strong>Strict-Transport-Security</strong> header, the browser records this information, so that future attempts to load the site using HTTP will automatically use HTTPS instead.<br/>
<br/>
When the expiration time specified by the <strong>Strict-Transport-Security</strong> header elapses, the next attempt to load the site via HTTP will proceed as normal instead of automatically using HTTPS.<br/>
<br/>
Whenever the <strong>Strict-Transport-Security</strong> header is delivered to the browser, it will update the expiration time for that site, so sites can refresh this information and prevent the timeout from expiring. Should it be necessary to disable <strong>Strict-Transport-Security</strong>, setting the max-age to 0 (over a https connection) will immediately expire the <strong>Strict-Transport-Security</strong> header, allowing access via http.<br/>
<strong>Recommendations: </strong><br/>Increase the time periodically.';

$_['about_Expect_CT']                           = 'The <strong>Expect-CT</strong> header prevents misissued certificates from being used by allowing websites to report and optionally enforce Certificate Transparency requirements. When this header is enabled the website is requesting the browser to verify whether or not the certificate appears in the public CT logs.<br/><br/>The <strong>Expect-CT</strong> header allows sites to opt in to reporting and/or enforcement of Certificate Transparency requirements, which prevents the use of misissued certificates for that site from going unnoticed.<br/>
<br/>
CT requirements can be satisfied by servers via any one of the following mechanisms:
<ul>
   <li>X.509v3 certificate extension to allow embedding of signed certificate timestamps issued by individual logs</li>
   <li>A TLS extension of type signed_certificate_timestamp sent during the handshake</li>
   <li>Supporting OCSP stapling (that is, the status_request TLS extension) and providing a SignedCertificateTimestampList</li>
</ul>
<br/><br/>
<strong>Recommendations: </strong><br/>max-age: Increase the time periodically<br/>Report-Uri: https://report-uri.cloudflare.com/cdn-cgi/beacon/expect-ct';

$_['about_Content_Security_Policy']             = 'The content-security-policy header provides an additional layer of security. This policy helps prevent attacks such as Cross Site Scripting (XSS) and other code injection attacks by defining content sources which are approved and thus allowing the browser to load them.
<br/><br/>
All major browsers currently offer full or partial support for content security policy. And it won’t break delivery of the content if it does happen to be delivered to an older browser, it will simply not be executed.
<br/><br/>
There are many directives which you can use with content security policy. This example below allows scripts from both the current domain (defined by ‘self’) as well as google-analytics.com.<br/><strong>Content Security Policy</strong> (CSP) is an added layer of security that helps to detect and mitigate certain types of attacks, including Cross Site Scripting (XSS) and data injection attacks. These attacks are used for everything from data theft to site defacement to distribution of malware.<br/>
<br/>
CSP is designed to be fully backward compatible (except CSP version 2 where there are some explicitly-mentioned inconsistencies in backward compatibility; more details here section 1.1). Browsers that don\'t support it still work with servers that implement it, and vice-versa: browsers that don\'t support CSP simply ignore it, functioning as usual, defaulting to the standard same-origin policy for web content. If the site doesn\'t offer the CSP header, browsers likewise use the standard same-origin policy.<br/>
<br/>
To enable CSP, you need to configure your web server to return the <strong>Content-Security-Policy</strong> HTTP header (sometimes you will see mentions of the X-Content-Security-Policy header, but that\'s an older version and you don\'t need to specify it anymore).<br/><br/><strong>Recommendations: </strong><br/>upgrade-insecure-requests';

$_['about_X_HTTP_Method_Override']             = 'In certain situations (for example, when the service or its consumers are behind an overzealous corporate firewall, or if the main consumer is a web page), only the GET and POST HTTP methods might be available. In such a case, it is possible to emulate the missing verbs by passing a custom header in the requests.
<br/><br/>
For example, resource updates can be handled using POST requests by setting a custom header (for example, X-HTTP-Method-Override) to PUT to indicate we are emulating a PUT request via a POST request.<br/><br/><strong>Recommendations: </strong><br/>Disabled';

$_['about_forward']                            = 'When a client connects to a server through a proxy or a load balancer, it’s imperative for an endpoint to use custom HTTP headers to be able to forward the identity of a the connecting client.
<br/><br/>
X-Forwarded-For (XFF) header is one of the mostly used HTTP header for that purpose. It serves a place where every forwarding node uses to store its direct client’s IP address using a comma as the separator forming a historical HTTP connection path. However HTTP is a text-based standard and it’s super easy to forge any part of it’s content. 
<br/><br/>
 By forging XFF header in this way the client may reach unauthorized parts of an application, create possible denial of service attacks or forge IP addresses logged. 
 <br/>
<strong>Forwarded</strong>
<p>Contains information from the client-facing side of proxy servers that is altered or lost when a proxy is involved in the path of the request.</p>
<strong>X-Forwarded-For</strong>
<p>Identifies the originating IP addresses of a client connecting to a web server through an HTTP proxy or a load balancer.</p>
<strong>X-Forwarded-Host</strong>
<p>Identifies the original host requested that a client used to connect to your proxy or load balancer.</p>
<strong>X-Forwarded-Proto</strong>
<p>Identifies the protocol (HTTP or HTTPS) that a client used to connect to your proxy or load balancer.</p>
<strong>Via</strong>
<p>Added by proxies, both forward and reverse proxies, and can appear in the request headers and the response headers.</p>
<strong>Recommendations: </strong><br/>Disabled';

$_['about_ranges']                              = 'The "Range" header is meant to be used to support partial downloads. A client may request just part of a file, instead of asking for the entire file.<br/>
<br/>
RFC 2616 is a bit ambiguous when it comes to "Range" headers. First of all, it introduces the "Accept-Ranges" header, which can be used by the server to signal that it supports the "Range" header. Next, it states that the client may send a request using a "Range" header anyway, even if the server doesn\'t advertise support for it. The server also has the option to send "Accept-Ranges: none" to explicitly state that it does not support this type of header.<br/>
<br/>
So what\'s the problem? It turns out that different HTTP clients appear to deal with "Range" headers slightly differently. In particular the iOS Podcast client requires support for the Range header, and will only download parts of the file if they are not supported. Apple recently advised iTunes publishers of this issue and requires content to be hosted on servers that support the Range header.<br/>
<br/>
For a server, this is usually not a problem, wouldn\'t it be for a recent Apache DoS attack that caused some to block Range requests.<br/>
<br/>
Range is used in the request, to ask for a particular range (or ranges) of bytes. Content-Range is used in the response, to indicate which bytes the server is giving you (which may be different than the range you requested), as well as how long the entire content is (if known).<br/><br/><strong>Recommendations: </strong><br/>Disabled';

$_['about_proxy']                              = '<strong>httpoxy</strong> is a set of vulnerabilities that affect application code running in CGI, or CGI-like environments. It comes down to a simple namespace conflict:
<ul>
    <li>RFC 3875 (CGI) puts the HTTP Proxy header from a request into the environment variables as HTTP_PROXY</li>
    <li>HTTP_PROXY is a popular environment variable used to configure an outgoing proxy</li>
</ul>
This leads to a remotely exploitable vulnerability. If you’re running PHP or CGI, you should block the Proxy header. Here’s how.
<br/><br/>
<strong>httpoxy</strong> is a vulnerability for server-side web applications. If you’re not deploying code, you don’t need to worry.
What can happen if my web application is vulnerable?
<br/><br/>
If a vulnerable HTTP client makes an outgoing HTTP connection, while running in a server-side CGI application, an attacker may be able to:
<ul>
    <li>Proxy the outgoing HTTP requests made by the web application</li>
    <li>Direct the server to open outgoing connections to an address and port of their choosing</li>
    <li>Tie up server resources by forcing the vulnerable software to use a malicious proxy</li>
</ul>
<strong>httpoxy</strong> is extremely easy to exploit in basic form. And we expect security researchers to be able to scan for it quickly. Luckily, if you read on and find you are affected, easy mitigations are available.<br/>
Isn’t this old news? Is this still a problem?
<br/><br/>
<strong>httpoxy</strong> was disclosed in mid-2016. If you’re reading about it now for the first time, you can probably relax and take your time reading about this quaint historical bug that hopefully no longer affects any of the applications you maintain. But you should verify that to your own satisfaction.
<br/><br/>
The content below this point reflects the original disclosure, and I’ll be leaving the site up and mostly unchanged, other than noting fix versions where I can. I guess I’m just saying: the time for urgency was last year.<br/><br/><strong>Recommendations: </strong><br/>Disabled';

$_['about_Feature_Policy']                      = 'The HTTP Feature-Policy header provides a mechanism to allow and deny the use of browser features in its own frame, and in content within any "iframe" elements in the document.<br/><br/>Feature Policy is being created to allow site owners to enable and disable certain web platform features on their own pages and those they embed. Being able to restrict the features your site can use is really nice but being able to restrict features that sites you embed can use is an even better protection to have.<br/><br/>Delivering a Feature Policy via HTTP response header is just as simple as issuing the other various security headers we have available to us. You simply need to decide the restrictions you\'d like to place on your page and build the policy to return.<br/><strong>Directives</strong>
<br/>
<i>ambient-light-sensor</i>
<p>Controls whether the current document is allowed to gather information about the amount of light in the environment around the device through the AmbientLightSensor interface.</p>
<i>autoplay</i>
<p>Controls whether the current document is allowed to autoplay media requested through the HTMLMediaElement interface. When this policy is enabled and there were no user gestures, the Promise returned by HTMLMediaElement.play() will reject with a DOMException. The autoplay attribute on <i>audio</i> and <i>video</i> elements will be ignored.</p>
<i>accelerometer</i>
<p>Controls whether the current document is allowed to gather information about the acceleration of the device through the Accelerometer interface.</p>
<i>battery</i>
<p>Controls whether the use of the Battery Status API is allowed. When this policy is enaled, the Promise returned by Navigator.getBattery() will reject with a NotAllowedError DOMException.</p>
<i>camera</i>
<p>Controls whether the current document is allowed to use video input devices. When this policy is enabled, the Promise returned by getUserMedia() will reject with a NotAllowedError DOMException.</p>
<i>display-capture</i>
<p>Controls whether or not the current document is permitted to use the getDisplayMedia() method to capture screen contents. When this policy is enabled, the promise returned by getDisplayMedia() will reject with a NotAllowedError if permission is not obtained to capture the display\'s contents.</p>
<i>document-domain</i>
<p>Controls whether the current document is allowed to set document.domain. When this policy is enabled, attempting to set document.domain will fail and cause a SecurityError DOMException to be be thrown.</p>
<i>encrypted-media</i>
<p>Controls whether the current document is allowed to use the Encrypted Media Extensions API (EME). When this policy is enabled, the Promise returned by Navigator.requestMediaKeySystemAccess() will reject with a DOMException.</p>
<i>execution-while-not-rendered</i>
<p>Controls whether tasks should execute in frames while they\'re not being rendered (e.g. if an iframe is hidden or display: none).</p>
<i>execution-while-out-of-viewport</i>
<p>Controls whether tasks should execute in frames while they\'re outside of the visible viewport.</p>
<i>fullscreen</i>
<p>Controls whether the current document is allowed to use Element.requestFullScreen(). When this policy is enabled, the returned Promise rejects with a TypeError DOMException.</p>
<i>geolocation</i>
<p>Controls whether the current document is allowed to use the Geolocation Interface. When this policy is enabled, calls to getCurrentPosition() and watchPosition() will cause those function\'s callbacks to be invoked with a PositionError code of PERMISSION_DENIED.</p>
<i>gyroscope</i>
<p>Controls whether the current document is allowed to gather information about the orientation of the device through the Gyroscope interface.</p>
<i>magnetometer</i>
<p>Controls whether the current document is allowed to gather information about the orientation of the device through the Magnetometer interface.</p>
<i>microphone</i>
<p>Controls whether the current document is allowed to use audio input devices. When this policy is enabled, the Promise returned by MediaDevices.getUserMedia() will reject with a NotAllowedError.</p>
<i>midi</i>
<p>Controls whether the current document is allowed to use the Web MIDI API. When this policy is enabled, the Promise returned by Navigator.requestMIDIAccess() will reject with a DOMException.</p>
<i>payment</i>
<p>Controls whether the current document is allowed to use the Payment Request API. When this policy is enabled, the PaymentRequest() constructor will throw a SecurityError DOMException.</p>
<i>picture-in-picture</i>
<p>Controls whether the current document is allowed to play a video in a Picture-in-Picture mode via the corresponding API.</p>
<i>publickey-credentials</i>
<p>Controls whether the current document is allowed to use Web Authentication API to create, store, and retreive public-key credentials.</p>
<i>speaker</i>
<p>Controls whether the current document is allowed to play audio via any methods.</p>
<i>sync-xhr</i>
<p>Controls whether the current document is allowed to make synchronous XMLHttpRequest requests.</p>
<i>usb</i>
<p>Controls whether the current document is allowed to use the WebUSB API.</p>
<i>wake-lock</i>
<p>Controls whether the current document is allowed to use Wake Lock API to indicate that device should not enter power-saving mode.</p>
<i>vr</i>
<p>Controls whether the current document is allowed to use the WebVR API. When this policy is enabled, the Promise returned by Navigator.getVRDisplays() will reject with a DOMException. Keep in mind that the WebVR standard is in the process of being replaced with WebXR.</p>
<i>xr-spatial-tracking</i>
<p>Controls whether or not the current document is allowed to use the WebXR Device API to interact with a WebXR session. </p><br/>';


// Warning Info - Notes
$_['warning_Strict_Transport_Security']         = '<strong>Note:</strong><br/>The <strong>Strict-Transport-Security</strong> header is ignored by the browser when your site is accessed using HTTP; this is because an attacker may intercept HTTP connections and inject the header or remove it. When your site is accessed over HTTPS with no certificate errors, the browser knows your site is HTTPS capable and will honor the <strong>Strict-Transport-Security</strong> header.';

$_['warning_Expect_CT']                         = '<strong>Note:</strong><br/>When a site enables the <strong>Expect-CT</strong> header, they are requesting that the browser check that any certificate for that site appears in public CT logs.<br/>
<br/>
Browsers ignore the <strong>Expect-CT</strong> header when sent over HTTP, the header only has effect on HTTPS connections.';

$_['warning_Referrer_Policy']                   = '<strong>Note:</strong><br/>The original header name <strong>Referer</strong> is a misspelling of the word "referrer". The <strong>Referrer-Policy</strong> header does not share this misspelling.';

$_['warning_X_Content_Type_Options']            = '<strong>Note:</strong><br/>X-Content-Type-Options only apply request-blocking due to nosniff for request destinations of "script" and "style". However, it also enables Cross-Origin Read Blocking (CORB) for HTML, TXT, JSON and XML files (excluding SVG image/svg+xml).';

$_['warning_X_Frame_Options']                   = '<strong>Note:</strong><br/>The Content-Security-Policy HTTP header has a frame-ancestors directive which obsoletes this header for supporting browsers.';

$_['warning_X_XSS_Protection']                  = '<strong>Note:</strong>
<ul>
<li>Chrome has an "Intent to Deprecate and Remove the XSS Auditor"</li>
<li>Firefox have not, and will not implement X-XSS-Protection</li>
<li>Edge have retired their XSS filter</li>
</ul>
<br/>This means that if you do not need to support legacy browsers, it is recommended that you use Content-Security-Policy without allowing unsafe-inline scripts instead.
';

// Legends
$_['legend_extension']                          = 'About Extension';
$_['legend_X_Powered_By']                       = 'X-Powered-By';
$_['legend_X_HTTP_Method_Override']             = 'X-HTTP-Method-Override';
$_['legend_proxy']                              = 'HTTP Proxy - (HTTPoxy)';
$_['legend_forward']                            = 'HTTP Forwards';
$_['legend_ranges']                             = 'HTTP Ranges';
$_['legend_X_XSS_Protection']                   = 'X-XSS-Protection';
$_['legend_X_Frame_Options']                    = 'X-Frame-Options';
$_['legend_X_Content_Type_Options']             = 'X-Content-Type-Options';
$_['legend_Referrer_Policy']                    = 'Referrer-Policy';
$_['legend_Content_Security_Policy']            = 'Content-Security-Policy';
$_['legend_Strict_Transport_Security']          = 'Strict-Transport-Security';
$_['legend_Expect_CT']                          = 'Expect-CT';
$_['legend_Feature_Policy']                     = 'Feature-Policy';

// Feature Policies
$_['type_accelerometer']			= 'Accelerometer';
$_['type_ambient_light_sensor']			= 'Ambient light sensor';
$_['type_autoplay']				= 'Autoplay';
$_['type_camera']				= 'Camera';
$_['type_fullscreen']				= 'Fullscreen';
$_['type_display_capture']			= 'Display capture';
$_['type_document_domain']			= 'Document domain';
$_['type_encrypted_media']			= 'Encrypted media';
$_['type_geolocation']				= 'Geolocation';
$_['type_gyroscope']				= 'Gyroscope';
$_['type_layout_animations']			= 'Layout animations';
$_['type_legacy_image_format']			= 'Legacy image format';
$_['type_magnetometer']				= 'Magnetometer';
$_['type_microphone']				= 'Microphone';
$_['type_midi']					= 'Midi';
$_['type_oversized_images']			= 'Oversized images';
$_['type_payment']				= 'Payment';
$_['type_picture_in_picture']			= 'Picture in picture';
$_['type_speaker']				= 'Speaker';
$_['type_sync_xhr']				= 'Sync xhr';
$_['type_unoptimized_images']			= 'Unoptimized images';
$_['type_unsized_media']			= 'Unsized media';
$_['type_usb']					= 'Usb';
$_['type_vr']					= 'Vr';
$_['type_vibrate']				= 'Vibrate';
$_['type_webauthn']				= 'Webauthn';

// Help Tooltip Feature Policies
$_['help_accelerometer']			= 'Controls whether the current document is allowed to gather information about the acceleration of the device through the Accelerometer interface.';
$_['help_ambient_light_sensor']			= 'Controls whether the current document is allowed to gather information about the amount of light in the environment around the device through the AmbientLightSensor interface.';
$_['help_autoplay']				= 'Controls whether the current document is allowed to autoplay media requested through the HTMLMediaElement interface. When this policy is enabled and there were no user gestures, the Promise returned by HTMLMediaElement.play() will reject with a DOMException. The autoplay attribute on "audio" and "video" elements will be ignored.';
$_['help_camera']				= 'Controls whether the current document is allowed to use video input devices. When this policy is enabled, the Promise returned by getUserMedia() will reject with a NotAllowedError DOMException.';
$_['help_fullscreen']				= 'Controls whether the current document is allowed to use Element.requestFullScreen(). When this policy is enabled, the returned Promise rejects with a TypeError DOMException.';
$_['help_display_capture']			= 'Controls whether or not the current document is permitted to use the getDisplayMedia() method to capture screen contents. When this policy is enabled, the promise returned by getDisplayMedia() will reject with a NotAllowedError if permission is not obtained to capture the display\'s contents.';
$_['help_document_domain']			= 'Controls whether the current document is allowed to set document.domain. When this policy is enabled, attempting to set document.domain will fail and cause a SecurityError DOMException to be be thrown.';
$_['help_encrypted_media']			= 'Controls whether the current document is allowed to use the Encrypted Media Extensions API (EME). When this policy is enabled, the Promise returned by Navigator.requestMediaKeySystemAccess() will reject with a DOMException.';
$_['help_geolocation']				= 'Controls whether the current document is allowed to use the Geolocation Interface. When this policy is enabled, calls to getCurrentPosition() and watchPosition() will cause those function\'s callbacks to be invoked with a PositionError code of PERMISSION_DENIED.';
$_['help_gyroscope']				= 'Controls whether the current document is allowed to gather information about the orientation of the device through the Gyroscope interface.';
$_['help_layout_animations']			= 'The HTTP Feature-Policy header layout-animations directive controls whether the current document is allowed to show layout animations.';
$_['help_legacy_image_format']			= 'The HTTP Feature-Policy header legacy-image-formats directive controls whether the current document is allowed to display images in legacy formats.';
$_['help_magnetometer']				= 'Controls whether the current document is allowed to gather information about the orientation of the device through the Magnetometer interface.';
$_['help_microphone']				= 'Controls whether the current document is allowed to use audio input devices. When this policy is enabled, the Promise returned by MediaDevices.getUserMedia() will reject with a NotAllowedError.';
$_['help_midi']					= 'Controls whether the current document is allowed to use the Web MIDI API. When this policy is enabled, the Promise returned by Navigator.requestMIDIAccess() will reject with a DOMException.';
$_['help_oversized_images']			= 'The HTTP Feature-Policy header oversized-images directive controls whether the current document is allowed to download and display large images.';
$_['help_payment']				= 'Controls whether the current document is allowed to use the Payment Request API. When this policy is enabled, the PaymentRequest() constructor will throw a SecurityError DOMException.';
$_['help_picture_in_picture']			= 'Controls whether the current document is allowed to play a video in a Picture-in-Picture mode via the corresponding API.';
$_['help_speaker']				= 'Controls whether the current document is allowed to play audio via any methods.';
$_['help_sync_xhr']				= 'Controls whether the current document is allowed to make synchronous XMLHttpRequest requests.';
$_['help_unoptimized_images']			= 'The HTTP Feature-Policy header unoptimized-images directive controls whether the current document is allowed to download and display unoptimized images.';
$_['help_unsized_media']			= 'The HTTP Feature-Policy header unsized-media directive controls whether the current document is allowed to change the size of media elements after the initial layout is complete. This restriction solves "layout instability" problem caused by providing default dimensions for images whose size is not specified in advance so that image doesn\'t change size after loading.';
$_['help_usb']					= 'Controls whether the current document is allowed to use the WebUSB API.';
$_['help_vr']					= 'Controls whether the current document is allowed to use the WebVR API. When this policy is enabled, the Promise returned by Navigator.getVRDisplays() will reject with a DOMException. Keep in mind that the WebVR standard is in the process of being replaced with WebXR.';
$_['help_vibrate']				= 'The HTTP Feature-Policy header vibrate directive controls whether the current document is allowed to trigger device vibrations via Vibration API.';
$_['help_webauthn']				= 'The HTTP Feature-Policy header publickey-credentials directive controls whether the current document is allowed to access Web Authentcation API, i.e, via navigator.credentials.create({publicKey: ...,...}) and navigator.credentials.get({publicKey: ...,...}). When this policy is enabled, any attempt to create or query public key credentials will result in an error.';

// Error
$_['error_permission']                          = 'Warning: You do not have permission to modify Opencart Security Headers!';
$_['error_expect_ct_report_uri']                = 'Warning: <strong>Expect-CT</strong> Report-Uri is empty. Recommendation: https://report-uri.cloudflare.com/cdn-cgi/beacon/expect-ct';
$_['error_data']                                = 'Warning: Please check the form carefully for errors!';
