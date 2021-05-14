function popupMessageBlockList() {
	var currentKeys = getCookie("popup-message");
	if (currentKeys) {
		currentKeys = JSON.parse(currentKeys);
		return currentKeys;
	}
	return [];
}

function addPopupMessageToBlockList() {
	var container = document.getElementById("popup-message-outer");
	var popupMessage = container.getElementsByClassName("popup-message");
	var messageKey = popupMessage[0].getAttribute("key");
	if (popupMessage.length > 0) {
		if (messageKey) {
			var messageKeys = [messageKey];
			console.log(messageKeys);
			
			var currentKeys = popupMessageBlockList();
			if (!currentKeys.includes(messageKey)) {
				messageKeys = messageKeys.concat(currentKeys);
			}
		
			console.log(messageKeys);
			writeCookie("popup-message", JSON.stringify(messageKeys), 1);
		}
	}
	
	dismissPopupMessage();
}

function dismissPopupMessage() {
	//console.log("container clicked");
	container = document.getElementById("popup-message-outer");
    document.body.classList.remove("popup-message-showing");
	
	var popupMessage = container.getElementsByClassName("popup-message");
	if (popupMessage.length > 0) {
		container.removeChild(popupMessage[0]);
	}
	showPopupMessage();
}

function showPopupMessage() {
	if (!document.body.classList.contains("popup-message-showing")) {
	  var popupMessage = document.getElementsByClassName("popup-message");
	  console.log(popupMessage);
	  if (popupMessage.length > 0) {
		var item = popupMessage[0];
		var messageKey = item.getAttribute("key");
		var url = item.getAttribute("url");
		var expires = item.getAttribute("expires");
		if (expires) {
			expires = new Date(expires);
		}
		console.log("key: " + messageKey + ", url: " + url + ", expires: " + expires);	
		if 	(
				(!url || (url && equalUrls(url, window.location)) ) &&
				(!popupMessageBlockList().includes(messageKey)) &&
				(!expires || (expires > new Date()))
			) {
			item.classList.add("centered");
		    var container = document.getElementById("popup-message-outer");
		    document.body.classList.add("popup-message-showing");
		    container.appendChild(item);
		}
		else {
			item.parentNode.removeChild(item);
			showPopupMessage();
		}
	  }
	}
}

function showTopMessage() {
	var topMessage = document.getElementsByClassName("top-message");
	var container = document.getElementById("top-message-container");
	var demo = document.getElementById("top-message-outer-demo");
	
	if (!container.hasChildNodes()) {
		for (let item of topMessage) {
			var url = item.getAttribute("url");
			if (url == null || (url && equalUrls(url, window.location))) {
				var messageOuter = demo.cloneNode(true);
				messageOuter.classList.remove("demo");
				messageOuter.appendChild(item);
				container.appendChild(messageOuter);
			}
		}
	}
}

function equalUrls(url1, url2) {
  return (
  	url1 && url2 &&
    new URL(url1, "http://example.com").pathname ===
    new URL(url2, "http://example.com").pathname
  );
}
ready(function () {
	showPopupMessage();
	showTopMessage();
});