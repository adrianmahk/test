<!DOCTYPE html>
<html>

<head>
    <title>GlassNote 2.0</title>
    <meta charset="UTF-8" />
    <meta content='width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1' name='viewport' />
    <link href="/assets/manifest_glassnote<?php echo (strpos(__FILE__, 'dev') > 0) ? '_dev.json' : '.json' ?>" rel='manifest' />
    <meta content='yes' name='apple-mobile-web-app-capable' />
    <meta content='black' name='apple-mobile-web-app-status-bar-style' />
    <meta content='GlassNote' name='apple-mobile-web-app-title' />
    <link href='/icons/app_icon_glassnote2-256.png' rel='apple-touch-icon' sizes='256x256' />
    <link href='/icons/app_icon_glassnote2-512.png' rel='apple-touch-icon' sizes='512x512' />

    <link rel="stylesheet" href="/assets/blog.css?t=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/blog.css')?>" />
    <link rel="stylesheet" href="/assets/styles.css?t=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/styles.css')?>" />
    <link rel="stylesheet" href="/assets/display-messages.css?t=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/display-messages.css')?>" />
    <style>
        body {
            background-color: #1d9f97;
        }
        .bg-div {
            background-position: center bottom;
            background-image: url(/assets/bg1.jpg<?php echo '?t='. filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/bg1.jpg')?>);
        }
    </style>
    <script>
        function loadIndie() {
            // For override
        }
    </script> 
    <script src="/scripts/blog-ui-ajax.js?t=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/scripts/blog-ui-ajax.js')?>""></script>
    <script src="/tinymce/tinymce.min.js"></script>
    <script src="/scripts/display-messages.js?t=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/scripts/display-messages.js')?>""></script>
    <script>
        var dfreeBodyConfig = {
            selector: '.editor-body',
            menubar: false,
            // inline: true,
            inline: false,
            plugins: [
                // 'autolink',
                // 'link',
                // 'lists',
                // 'visualblocks',
                // 'powerpaste',
                'quickbars',
                // 'textpattern'
                // 'paste'
                // 'searchreplace'
            ],
            valid_classes: {
                '*': 'quote', // Global classes
                'img': 'lazyload' // Link specific classes
            },
            toolbar: false,
            quickbars_insert_toolbar: 'undo redo paste',
            quickbars_selection_toolbar: 'undo redo cut copy paste',
            contextmenu: 'undo redo cut copy paste',
            element_format: 'xhtml',
            forced_root_block: "div",
            block_unsupported_drop: false,
            init_instance_callback: function (editor) {
                loadFromLocalStorage(initEventListeners);
            },
            setup: function(editor) {
                editor.on("init",  function() {
                    // console.log("on init");
                    // overrides, 1-6 for header, 7 - p, 8 - div, 9 - address
                    editor.addShortcut("access+1", "", "");
                    editor.addShortcut("access+2", "", "");
                    editor.addShortcut("access+3", "", "");
                    editor.addShortcut("access+4", "", "");
                    editor.addShortcut("access+5", "", "");
                    editor.addShortcut("access+6", "", "");
                    editor.addShortcut("access+7", "", "");
                    editor.addShortcut("access+8", "", "");
                    editor.addShortcut("access+9", "", "");
                    editor.addShortcut("meta+k", "", "");
                    editor.addShortcut("meta+shift+13", "", function (){
                        editor.execCommand('mceInsertContent', false, '\u2028\n');
                        editor.execCommand('mceInsertNewLine');
                    });
                    // editor.addShortcut("meta+b", "", "");
                    // editor.addShortcut("meta+i", "", "");
                    editor.addShortcut("meta+u", "", "");

                    // for toolbar functions
                    // editor.addShortcut("meta+shift+C", "Save to file (.txt)", saveToFile);
                    // editor.addShortcut("access+a", "Copy to clipboard", copyToClipboard);
                    // editor.addShortcut("meta+alt+o", "Load from file (.txt)", readFromFile);
                    // editor.addShortcut("meta+b", "Customize Background", function () {
                    //     document.body.classList.contains("user-bg") ? removeBg() : changeBg()
                    // });
                });
            }
        };
        // tinymce.init(dfreeBodyConfig);
    </script>

	<script>
		function setupServiceWorker() {
			if (!document.body.classList.contains('error404')) {
				if ('serviceWorker' in navigator) {
					// navigator.serviceWorker.register("/sw.js?t=<?php echo filemtime(__FILE__); ?>", {scope: "/"}).then(function(registration) {
					navigator.serviceWorker.register("<?php echo ((strpos(__FILE__, 'dev') > 0) ? '/sw-dev.js' : '/sw.js') . '?t=' . filemtime(__FILE__); ?>", {scope: "/"}).then(function(registration) {
						console.log('Service worker registration succeeded:', registration);
					}, /*catch*/ function(error) {
						console.log('Service worker registration failed:', error);
					});
				}
				navigator.serviceWorker.addEventListener('message', event => {
					console.log(`The service worker sent me a message: ${event.data}`);
				});
			}
		}
		ready(setupServiceWorker);
	</script>

    <script>
        var timer = 0;
        function initEventListeners() {
            window.addEventListener("pagehide", function () {
                saveToLocalStorage();
                // clearAutoSaveTimer();
            });
            window.addEventListener("visibilitychange", function () {
                if (document.visibilityState === 'hidden') {
                    saveToLocalStorage();
                }
            });
            window.addEventListener("pageshow", function (event) {
                if (event.persisted) {
                    loadFromLocalStorage();
                }
            });

            var editorBody = document.getElementById('editor-body');
            // editorBody.addEventListener('input', setAutoSaveTimeout);
            // const observer = new MutationObserver(setAutoSaveTimeout);
            // if (editorBody && observer) {
            //     observer.observe(editorBody, { attributes: false, childList: false, subtree: false});
            // }
            // else if(window.addEventListener) {
            // // Normal browsers
            //     editorBody.addEventListener('DOMSubtreeModified', setAutoSaveTimeout);
            //     // editorBody.addEventListener('DOMSubtreeModified', setAutoSaveTimeout);
            // } else if(window.attachEvent) {
            //     // IE
            //     editorBody.attachEvent('DOMSubtreeModified', setAutoSaveTimeout);
            // }
            editorBody.addEventListener("copy", handleCopyEvent);
            editorBody.addEventListener("paste", function (e){
                e.preventDefault();
                var text = '';
                if (e.clipboardData || e.originalEvent.clipboardData) {
                    text = (e.originalEvent || e).clipboardData.getData('text/plain');
                } else if (window.clipboardData) {
                    text = window.clipboardData.getData('Text');
                }
                text = text.replaceAll(/\u2028(?=[^\n])/g, '\u2028\n');
                if (document.queryCommandSupported('insertText')) {
                    document.execCommand('insertText', false, text);
                } else {
                    document.execCommand('paste', false, text);
                }

                return false;
            });
            window.addEventListener("dragover", (e) => {
                e.preventDefault();
            });
            window.addEventListener("drop", handleDragEvent);
            window.addEventListener("keydown", handleKeypressEvent);
            // showPopupMessage();
            hidePageLoading();
            document.body.classList.remove("tiny-loading");
        }

        ready(function () {
            // addButtonToTopBar();
            //moveAboutMessageToAboveFooter();
            loadBg();
            initBodyClassesForOpacityAndFontSize();
            initEventListeners();
            loadFromLocalStorage();
        });
        
        function handleDragEvent(e) {
            // console.log(e);
            e.preventDefault();
            e.stopPropagation();

            let items = null;
            if (e.dataTransfer.items) {
                items = e.dataTransfer.items;
            }
            else if (e.dataTransfer.files) {
                items = e.dataTransfer.files;
            }
            if (items) {
                for (let item of items) {
                    console.log(item.type);
                    if (item.kind === 'file' && (item.type.match('text/plain') || item.type.match('text/markdown'))) {
                        let file = item.getAsFile();
                        const reader = new FileReader();
    
                        reader.addEventListener("load", readFileCallBack, false);
                        reader.readAsText(file);
                    }
                    else {
                        alert("不支援的格式");
                    }
                }
            }
        }
        function handleCopyEvent(e) {
            console.log("copy");
            event.preventDefault();
            const selection = document.getSelection();
            event.clipboardData.setData('text/plain', selection.toString().replaceAll("\u2028\n", "\u2028"));
        }
        function handleKeypressEvent(event){
            // console.log(event);
            // console.log('keypress: ', event.which);
            if (event.metaKey || event.controlKey) {
                let key = event.key.toUpperCase();
                console.log(key);

                if (!event.shiftKey) {
                    let controlKeys = ['S', 'O']; //s, o
                    if (controlKeys.includes(key)) {
                        // if (event)
                        event.preventDefault();
                        if (!event.repeat) {
                            switch (key) {
                                case 'S': setTimeout(saveToFile, 1000);  break;
                                case 'O': readFromFile(); break;
                            }
                        }
                    }
                }
                else {
                    let controlShiftKeys = ['O', 'M', 'I', 'D']; //o, m, i, d
                    if (controlShiftKeys.includes(key)) {
                        event.preventDefault();
                        if (!event.repeat) {
                            switch (key) {
                                case 'O': toggleOpacity(); break;
                                case 'M': toggleExpandedMenu(); break;
                                case 'I': changeFontSize(); break;
                                case 'D': darkMode(); break;
                            }
                        }
                        
                    }
                }
            }
        }

        function removeStylesAndReplacePToDiv(el) {
            // el.removeAttribute('style');
            // el.removeAttribute('class');
            
            el.innerText = el.innerText;
            if (el.tagName == "P" || el.tagName == "p") {
                // el.outerHTML = el.outerHTML.replace(/(?:<)p|(?:<\/)p/g,"div");
                el.outerHTML = el.outerHTML.replace(/(?=<)p/g,"div");
            }
        }
        function textAreaAdjust() {
            let element = document.getElementById("editor-body");
            element.style.height = "1px";
            element.style.height = (25+element.scrollHeight)+"px";
        }

        function setAutoSaveTimeout() {
            // this function will run each time the content of the DIV changes
            // console.log('changed');
            textAreaAdjust();
            clearTimeout(timer);
            timer = setTimeout(() => {
                saveToLocalStorage();    
            }, 5000);
        }

        function saveToLocalStorage() {
            return;
            var editor = document.getElementById("editor-body");
            var lastVer = localStorage.getItem('content');
            // var currentVer = editor.innerHTML;
            var currentVer = editor.value;

            if (lastVer) {
                if (lastVer == currentVer) {
                    return;
                }
            }

            if (editor) {
                var textContent = editor.textContent;
                var textContent = editor.value;
                if (textContent != "") {
                    try {
                        localStorage.setItem('content', currentVer);
                        var date = new Date();
                        localStorage.setItem('last-saved-time', date.toLocaleDateString("en-GB") + " " +date.toLocaleTimeString("en-GB", { hour: '2-digit', minute: '2-digit' }));
                    }
                    catch (e) {
                        alert("瀏覽器內存已滿！儲存失敗，請記得帶走文字內容！");
                        console.log(e.toString());
                        console.dir(e);
                    }

                    updateLastSavedMsg();
                    console.log("saved version: " + date.toLocaleString());
                    // console.log(currentVer);
                }
            }
        }
        function loadFromLocalStorage(fn = null) {
            var text = localStorage.getItem('content');
            if (text) {
                var editor = document.getElementById("editor-body");
                let temp = document.createElement("div");
                temp.innerHTML = text.replaceAll(/\u2028(?!<br \/>|<br>)/g, '\u2028<br />');
                temp.setAttribute("class", "fake-editor editor-body entry-content");
                document.body.appendChild(temp);
                // editor.innerHTML = text.replaceAll(/\u2028(?!<br \/>|<br>)/g, '\u2028<br />');
                editor.value = temp.innerText;
                document.body.removeChild(temp);
                textAreaAdjust();
                updateLastSavedMsg();
                console.log("loadedFromStorage");
            }
            if (fn) {
                fn();
            }
        }
        function updateLastSavedMsg() {
            var lastSavedMsg = document.getElementsByClassName("last-saved-msg");
            var lastSavedTime = localStorage.getItem("last-saved-time");
            if (lastSavedTime != "") {
                for (var i = 0; i < lastSavedMsg.length; i++) {
                    lastSavedMsg[i].innerText = lastSavedTime;
                }

            }
            else {
                for (var i = 0; i < lastSavedMsg.length; i++) {
                    lastSavedMsg[i].innerText = "-";
                }
            }
        }

        function clearCurrentAndLocalStorage() {
            if (confirm("確定要清空所有內容? 這個操作將不可復原")) {
                localStorage.removeItem("content");
                localStorage.removeItem("last-saved-time");
                var editor = document.getElementById("editor-body");
                if (editor) {
                    editor.innerHTML = "<div></div>";
                }
                updateLastSavedMsg();
            }
        }
        function updateTitle() {
            var postTitle = document.getElementsByClassName("post-title entry-title")[0];
            var editor = document.getElementById("editor-body");
            var content = editor.innerText;
            postTitle.innerHTML = getFirstLine(content);
        }
        function getFirstLine(str) {
            var breakIndice = [str.indexOf("\n"), 100];
            breakIndice = breakIndice.filter(breakIndice => breakIndice >= 0);
            var breakIndex = Math.min(...breakIndice);
            if (breakIndex === -1) {
                return str;
            }

            return str.substr(0, breakIndex);
        }
        function copyToClipboard() {
            var fakeEditor = document.createElement("div");

            fakeEditor.innerHTML = document.getElementById("editor-body").innerHTML;
            fakeEditor.setAttribute("class", "fake-editor editor-body entry-content");
            fakeEditor.addEventListener('copy', handleCopyEvent);
            document.body.appendChild(fakeEditor);

            var range = document.createRange();
            range.selectNodeContents(fakeEditor);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);

            var result = document.execCommand('copy');
            fakeEditor.parentNode.removeChild(fakeEditor);

            saveToLocalStorage();
        }
        function showMoreInfo() {
            var classList = document.body.classList;
            if (!classList.contains("show-info")) {
                document.body.classList.add("show-info");
            }
            else {
                document.body.classList.remove("show-info");
            }
        }
        function addButtonToTopBar() {
            var topBar = document.getElementById("top-bar");
            var editorButtons = document.getElementById("editor-buttons");
            topBar.append(...editorButtons.childNodes);
            editorButtons.innerHTML = "";
        }
        function moveAboutMessageToAboveFooter() {
            var msg = document.getElementById("glassnote-about-msg");
            var footer = document.getElementById("footer");
            footer.parentNode.insertBefore(msg, footer);
        }
        function changeBg() {
            const bgDivCust = document.getElementById('bg-div-cust');
            const reader = new FileReader();

            reader.addEventListener("load", function () {
                bgDivCust.style.backgroundImage = `url(${reader.result})`;
                document.body.classList.add("user-bg");
                try {
                    localStorage.setItem("user-bg", reader.result);
                }
                catch (e) {
                    alert("此圖片檔案過大 (請選擇 1MB 以內) 無法儲存至瀏覽器內存");
                    console.log(e.toString());
                    console.dir(e);
                }
            }, false);
            var input = document.createElement("input");
            input.setAttribute("type", "file");
            input.setAttribute("accept", "image/*, video/*");
			input.addEventListener('click', function () {
				input.parentNode.removeChild(input);
			});
            input.addEventListener('change', function () {
                const image = this.files[0];
                console.log(image.size);
                if (image) {
					if (image.type.match("image") || image.type.match("video")) {
						reader.readAsDataURL(image);
					}
					else {
						alert("不支援的格式");
					}
                }
            }, false);

            document.body.appendChild(input);
            input.click();

        }

        function loadBg() {
            if (localStorage.getItem("user-bg") != null) {
                const bgDivCust = document.getElementById('bg-div-cust');
                bgDivCust.style.backgroundImage = `url(${localStorage.getItem("user-bg")})`;
                document.body.classList.add("user-bg");
            }
        }

        function removeBg() {
            localStorage.removeItem("user-bg");
            document.body.classList.remove("user-bg");
        }

        function escapeHTML(html) {
            var fn = function(tag) {
                var charsToReplace = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&#34;'
                };
                return charsToReplace[tag] || tag;
            }
            return html.replace(/[&<>"]/g, fn);
        }

        function readFileCallBack() {
            if (confirm("從 txt 檔匯入文字? 現有內容將會被覆寫")) {
                const editorBody = document.getElementById("editor-body");
                editorBody.innerHTML = "<div></div>";
                console.log(this.result);
                let text = escapeHTML(this.result);
                text = text.replaceAll(/\u2028(?=.)/g, '\u2028\n');
                text = text.replaceAll(/  \n/g, '\u2028\n');
                console.log(text);
                let htmlContent = "";
                const lines = text.split(/\r?\n/);
                for(let line of lines){
                    if (line == "") {
                        line = "<br />";
                    }
                    htmlContent += "<div>" + line + "</div>";
                }
                console.log("readFromFile success");
                
                editorBody.innerHTML = htmlContent;
                saveToLocalStorage();
            }
        }

        function readFromFile() {
            const reader = new FileReader();

            reader.addEventListener("load", readFileCallBack, false);
            var input = document.createElement("input");
            input.setAttribute("type", "file");
            input.setAttribute("accept", "text/plain, text/markdown");
			input.addEventListener('click', function () {
				input.parentNode.removeChild(input);
			});
            input.addEventListener('change', function () {
                const file = this.files[0];
                console.log("fileSize: " + file.size);
                if (file) {
                    // document.body.setAttribute('filename', file.name);
                    if (file.type.match('text/plain') || file.type.match('text/markdown')) {
                        reader.readAsText(file);
                    }
                    else {
                        alert('檔案格式不支援 (只接受 .txt 純文字文件)');
                        console.log(file.type);
                    }
                }
            }, false);

            document.body.appendChild(input);
            input.click();
        }

        function saveToFile() {
            let textFile = null,
                makeTextFile = function (text) {
                    var data = new Blob([text], { encoding: "UTF-8", type: "text/plain;charset=UTF-8" });
                    if (textFile !== null) {
                        window.URL.revokeObjectURL(textFile);
                    }
                    textFile = window.URL.createObjectURL(data);
                    return textFile;
                };

            let link = document.createElement('a');
            link.setAttribute('target', '_blank');
            let fileName = '';
            if (document.body.getAttribute('filename')) {
                fileName = document.body.getAttribute('filename');
            }
            else {
                fileName = getFirstLine(content);
            
                if (!fileName || fileName == "") {
                    fileName = "GlassNote";
                    const date = new Date();
                    let ye = new Intl.DateTimeFormat('en', { year: 'numeric'}).format(date);
                    let mo = new Intl.DateTimeFormat('en', { month: '2-digit'}).format(date);
                    let da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);
                    let ho = new Intl.DateTimeFormat('en', { hour: 'numeric', hour12: false}).format(date);
                    let mi = new Intl.DateTimeFormat('en', { minute: 'numeric'}).format(date);
                    fileName = "GlassNote-"+`${ye}${mo}${da}`+'-'+`${ho}${mi}`;
                    // console.log(fileName);
                }
                fileName = fileName + ".txt";
            }
            
            link.setAttribute('download', fileName);
            link.href = makeTextFile(content);
            link.addEventListener('click', function () {
                document.body.removeChild(link);
            });
            document.body.appendChild(link);

            link.click();
        }

        function saveToFileOld() {
            // let content = document.getElementById("editor-body").innerText;

            // move all texts into a single div with brs, to avoid Chrome on9 behaviour
            let fakeEditor = document.createElement("div");
            fakeEditor.innerHTML = document.getElementById("editor-body").innerHTML;
            fakeEditor.setAttribute("id", "fake-editor");
            fakeEditor.setAttribute("class", "fake-editor editor-body entry-content");
            document.body.appendChild(fakeEditor);
            
            let emptyDivBrs = fakeEditor.querySelectorAll("div > br:only-child");
            for (let item of emptyDivBrs) {
                item.parentNode.outerHTML = item.parentNode.innerText.match('\u2028\n') ? '\u2028' : '' + "<br />";
            }
            fakeEditor.innerHTML = fakeEditor.innerHTML.replaceAll("</div>", "<br />");
            fakeEditor.innerHTML = fakeEditor.innerHTML.replaceAll("<div>", "");
            
            
            let content = fakeEditor.innerText.replaceAll('\u2028\n', '\u2028');
            if (content.slice(-1) == "\n") {
                content = content.slice(0, -1);
            }
            document.body.removeChild(fakeEditor);
            // console.log(content);

            let textFile = null,
                makeTextFile = function (text) {
                    var data = new Blob([text], { encoding: "UTF-8", type: "text/plain;charset=UTF-8" });
                    if (textFile !== null) {
                        window.URL.revokeObjectURL(textFile);
                    }
                    textFile = window.URL.createObjectURL(data);
                    return textFile;
                };

            let link = document.createElement('a');
            link.setAttribute('target', '_blank');
            let fileName = '';
            if (document.body.getAttribute('filename')) {
                fileName = document.body.getAttribute('filename');
            }
            else {
                fileName = getFirstLine(content);
            
                if (!fileName || fileName == "") {
                    fileName = "GlassNote";
                    const date = new Date();
                    let ye = new Intl.DateTimeFormat('en', { year: 'numeric'}).format(date);
                    let mo = new Intl.DateTimeFormat('en', { month: '2-digit'}).format(date);
                    let da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(date);
                    let ho = new Intl.DateTimeFormat('en', { hour: 'numeric', hour12: false}).format(date);
                    let mi = new Intl.DateTimeFormat('en', { minute: 'numeric'}).format(date);
                    fileName = "GlassNote-"+`${ye}${mo}${da}`+'-'+`${ho}${mi}`;
                    // console.log(fileName);
                }
                fileName = fileName + ".txt";
            }
            
            link.setAttribute('download', fileName);
            link.href = makeTextFile(content);
            link.addEventListener('click', function () {
                document.body.removeChild(link);
            });
            document.body.appendChild(link);
            // setTimeout(() => {
                link.click();
            // }, 1000);

            // wait for the link to be added to the document
            // window.requestAnimationFrame(function () {
            //     var event = new MouseEvent('click');
            //     link.dispatchEvent(event);
            // });
        }

        function initBodyClassesForOpacityAndFontSize() {
            const element = document.getElementById("left-sidebar");
            const fontSizes = ["f12px", "f14px", "f15px", "f16px", "f18px"];
            if (![...document.body.classList].some(className => fontSizes.indexOf(className) !== -1)) {
                document.body.classList.add("f14px");
            }

            let opacity = getCookie("opacity");
            if (opacity != "") {
                document.body.classList.add(opacity);
            }
            else {
                document.body.classList.add("opacity-10");
            }

        }

        function toggleOpacity() {
            var body = document.body;
            var next_opacity = "opacity-20";

            if (body.classList.contains("opacity-0")) {
                next_opacity = "opacity-20";
            }
            else if (body.classList.contains("opacity-10")) {
                next_opacity = "opacity-20";
            }
            else if (body.classList.contains("opacity-20")) {
                next_opacity = "opacity-40";
            }
            else if (body.classList.contains("opacity-30")) {
                next_opacity = "opacity-40";
            }
            else if (body.classList.contains("opacity-40")) {
                next_opacity = "opacity-60";
            }
            else if (body.classList.contains("opacity-50")) {
                next_opacity = "opacity-60";
            }
            else if (body.classList.contains("opacity-60")) {
                next_opacity = "opacity-0";
            }
            else if (body.classList.contains("opacity-70")) {
                next_opacity = "opacity-0";
            }
            else if (body.classList.contains("opacity-80")) {
                next_opacity = "opacity-0";
            }
            else if (body.classList.contains("opacity-90")) {
                next_opacity = "opacity-0";
            }

            body.classList.remove("opacity-0", "opacity-10", "opacity-20", "opacity-30", "opacity-40", "opacity-50", "opacity-60", "opacity-70", "opacity-80", "opacity-90" );
            body.classList.add(next_opacity);
            writeCookie("opacity", next_opacity);
        }

        function toggleExpandedMenu() {
            document.body.classList.toggle("expanded-menu");
        }
    </script>
</head>

<body class="item-view tiny-loading page-loading">
    


    <script type="text/javascript">changeFontSizeInit(); darkModeInit();</script>
    <div class="bg-div" id="bg-div"></div>
    <div class="bg-div-cust" id="bg-div-cust"></div>
    <div class="loading-bar" id="loading-bar"></div>
    <div class="dark_mode_overlay" id="dark_mode_overlay"></div>
    <div class="page_body" id="page">
        <div class="page-upper-part"></div>
        <div class="centered top-bar-container">
            <div class="centered-top-container top-bar" id="top-bar">
                <div class="right-button-container  flat-icon-button ripple">
                    <div class="splash-wrapper"><span class="splash"
                            style="height: 64px; width: 64px; left: 16px; top: 3px;"></span></div>
                    <a class="return_link dark_mode_button" onclick="darkMode();" title="黑夜主題" href="javascript:void(0)">
                        <img class="png_icon light" src="/icons/moon.png">
                        <img class="png_icon dark" src="/icons/moon_dark.png">
                    </a>
                </div>
                <div class="toolbar">
                    <a class="return_link hamburger-button" href="javascript:void(0)" onclick="toggleExpandedMenu();" title="選單">
                        <img class="png_icon light" src="/icons/hamburger.png" />
                        <img class="png_icon dark" src="/icons/hamburger_dark.png" />
                    </a>
                    <div class="expanded-menu-container">
                        <div class="expanded-menu">
                            <div style="display: table-row;">
                                    <a class="return_link hamburger-button" href="javascript:void(0)" onclick="toggleExpandedMenu();" title="選單">
                                        <img class="png_icon light" src="/icons/hamburger.png" />
                                        <img class="png_icon dark" src="/icons/hamburger_dark.png" />
                                    </a>
                                <!-- </button> -->
                                <div class="expanded-menu-items">
                                    <tbc>GlassNote 2.0</tbc><em> - Build: 11 May 2022</em>
                                    <br />
                                    <!-- <h3>上次儲存：<span class='last-saved-msg'>-</span>　　　<a style=""
                                        href="javascript:void(0)" onclick="clearCurrentAndLocalStorage();">清空</a></h3> -->
                                    <button class="pill-button ripple"
                                        onclick="readFromFile();">從檔案匯入</button>
                                    <button class="pill-button ripple"
                                        onclick="saveToFile();">儲存至檔案</button>
                                    <button class="pill-button ripple"
                                        onclick="copyToClipboard();">複製至剪貼簿</button>
                                    <span>
                                        <button class="pill-button ripple" id="change-bg-button"
                                            onclick="changeBg();">自訂桌布</button>
                                        <button class="pill-button ripple" id="remove-bg-button"
                                            onclick="removeBg();">移除桌布</button>
                                    </span>
                                    <!-- <button class="pill-button ripple" onclick="">字數：</button> -->
                                    <!--<br />-->
                                    <span>
                                        <button class="pill-button ripple opacity-button"
                                            onclick="toggleOpacity();">透明度：0%</button>
                                        <button class="pill-button ripple opacity-button"
                                            onclick="toggleOpacity();">透明度：10%</button>
                                        <button class="pill-button ripple opacity-button"
                                            onclick="toggleOpacity();">透明度：20%</button>
                                        <button class="pill-button ripple opacity-button"
                                            onclick="toggleOpacity();">透明度：30%</button>
                                        <button class="pill-button ripple opacity-button"
                                            onclick="toggleOpacity();">透明度：40%</button>
                                        <button class="pill-button ripple opacity-button"
                                        onclick="toggleOpacity();">透明度：50%</button>
                                        <button class="pill-button ripple opacity-button"
                                        onclick="toggleOpacity();">透明度：60%</button>
                                        <button class="pill-button ripple opacity-button"
                                        onclick="toggleOpacity();">透明度：70%</button>
                                        <button class="pill-button ripple opacity-button"
                                        onclick="toggleOpacity();">透明度：80%</button>
                                        <button class="pill-button ripple opacity-button"
                                        onclick="toggleOpacity();">透明度：90%</button>
                                    </span>
                                    <span>
                                        <button class="pill-button ripple font-size-button"
                                            onclick="changeFontSize();">字體：13px</button>
                                        <button class="pill-button ripple font-size-button"
                                            onclick="changeFontSize();">字體：14px</button>
                                        <button class="pill-button ripple font-size-button"
                                            onclick="changeFontSize();">字體：15px</button>
                                        <button class="pill-button ripple font-size-button"
                                            onclick="changeFontSize();">字體：16px</button>
                                        <button class="pill-button ripple font-size-button"
                                            onclick="changeFontSize();">字體：17px</button>
                                    </span>
                                    <span>
                                        <button class="pill-button ripple dark-mode-button-toolbar" onclick="darkMode();">黑夜主題：開</button>
                                        <button class="pill-button ripple dark-mode-button-toolbar" onclick="darkMode();">黑夜主題：關</button>
                                    </span>
                                    <br />
                                    <span style="display:table-row; font-style: italic;">
                                        <span style="display: table-cell;">上次儲存：<span class='last-saved-msg'>-</span></span>
                                        <a style="display: table-cell; min-width: 50px; padding: 0 5px; text-align: center;" href="javascript:void(0)" onclick="clearCurrentAndLocalStorage();">清空</a>
                                    </span>
                                    <!-- <span style="float:right; margin-right: 2em;">
                                        <a href="https://qingsky.hk/p/glassnote-about.html" target="_blank">關於
                                        GlassNote 2.0</a>
                                    </span> -->
                                </div>
                                <div class="toolbar-overlay" onclick="toggleExpandedMenu()"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-lower-part">
            
            <div class="centered" style="position: relative;">
                <div class="post-outer-container">
                    <div class="post-outer post">
                        <div class="post-body entry-content float-container">
                            <!-- <h3>上次儲存：<span class='last-saved-msg'>-</span>　　　<a style="float: right"
                                    href="javascript:void(0)" onclick="clearCurrentAndLocalStorage();">清空</a></h3> -->
                            <div class="editor-buttons" id="editor-buttons">
                                
                            </div>
                            <div class='editor' id='editor'>
                                <textarea class="editor-body entry-content" id='editor-body' changing='false' style="position: relative;" spellcheck="false"></textarea>
                            </div>
                            <hr />
                        </div>
                    </div>

                </div>

                

                <!-- Top Message will be displayed at bottom instead, but dont use it unless necessary-->
                <div class="top-message-container centered" id="top-message-container"></div>
                <div class="top-message-outer demo" id="top-message-outer-demo">
                    <img src="/icons/info.png"/>
                </div>

                <div id="glassnote-about-msg" class="glassnote-about-msg">
                    <!-- <p><button class="pill-button ripple" id="change-bg-button"
                            onclick="changeBg();">自訂桌布</button><button class="pill-button ripple" id="remove-bg-button"
                            onclick="removeBg();">移除桌布</button></p> -->
                    <!-- <h3>上次儲存：<span class='last-saved-msg'>-</span>　　　<a href="javascript:void(0)"
                            onclick="clearCurrentAndLocalStorage();">清空</a></h3> -->
                    <p><em>GlassNote 2.0，<a href="https://qingsky.hk/glassnote-about" 
                                target="_blank">按這裡了解更多</a></em></p>
                    <!-- <p><em>GlassNote 2.0</em></p> -->
                    <?php echo 'dev';?>
                </div>
            </div>
        </div>
        
        <!-- Popup Messages -->
        <div class="popup-message-container" id="popup-message-container">
            <div class="dim-overlay" onclick="dismissPopupMessage()"></div>
            <div class="popup-message-outer centered" id="popup-message-outer">
                <div style="order: 0; text-align: right;"><a class="flat-button" onclick="dismissPopupMessage()"><img src="/icons/cross.png" style="height: 24px; width: 24px"/></a>
                </div>
                <div style="order: 3; text-align: center; margin-top: .5em;"><a class="flat-button" id="popup-message-dismiss-button" onclick="addPopupMessageToBlockList()">今天不再顯示</a>
                </div>
            </div>
        </div>


        <!-- Insert Messages Here -->
        <!-- <div class="popup-message" key="archive-20210513001" expires="2021-05-20" url="">
            <p>《昔日的天空》已更新，目前開放 2017 年 5 月：<a href="https://archive.qingsky.hk">https://archive.qingsky.hk</a></p>
            <p>那年是我寫小說十周年，碰巧我替本站做了第一次大型更新，也是一個值得紀錄的日子</p>
        </div> -->
        <!-- <div class="top-message" key="archive-20210513001" >
            <p>歡迎瀏覽《昔日的天空》，目前開放 2017 年 5 月：<a href="https://archive.qingsky.hk">https://archive.qingsky.hk</a></p>
        </div> -->
        
    </div>
</body>

</html>