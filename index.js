(function(){
	$(document).ready(function(){
		$("#sc-nopic").click(function(){
			if ($(this).is(":checked")) {
				$("#pd-imgs").hide();
				$("#sc-gist .sc-rt").css({"width" : "100%"});
				$("#sc-gist .pd-tn").hide();
			}
			else {
				$("#pd-imgs").show();
				$("#sc-gist .sc-rt").css({"width" : "75%"});
				$("#sc-gist .pd-tn").show();
			};
		});
		$("#startChat").keyup(function(e){
			var charCode =  e.keyCode || e.which || e.charCode;
			if(charCode == 13 || charCode == 32){
				getGist();
			}
		});
		$("#startChat").bind("paste", function(){
			setTimeout(getGist, 100);
		});
		$("#startChat").blur(getGist);
		$("#parse").click(getGist);
		$("#sc-cls").click(function(){
			$("#sc-gist").hide();
		});
		$("#pd-nt, #pd-pre").click(showPrevNext);
		
		$("#samples a").click(addSample);
	});
})(jQuery);
function addSample(e){
	var curEl = $(e.currentTarget);
	$("#sc-gist").hide();
	$("#startChat").val(curEl.html());
	$("#parse").click();
}
function showPrevNext(e){
	var curEl = $(e.currentTarget);
	
	if(!curEl.hasClass("dis")){
		var gistImages = $("#pd-imgs"), curImgNo = parseInt(gistImages.attr("cur")) || 0, totalImages = $("img", gistImages);
		var curImg = $(totalImages[curImgNo]), totalLength = totalImages.length;
		if (curEl.attr("id") == "pd-pre") {
			curImg.hide();
			curImg.prev().show();
			gistImages.attr("cur", (--curImgNo));
			$("#pd-nt").removeClass("dis");
			if((curImgNo) < 1){
				$("#pd-pre").addClass("dis");
			}
			$("#pd-imgCnt").html((curImgNo+1) + " of " + totalLength);
		}
		else if (curEl.attr("id") == "pd-nt") {
			curImg.hide();
			curImg.next().show();
			gistImages.attr("cur", (++curImgNo));
			$("#pd-pre").removeClass("dis");
			if((curImgNo+1) >= totalLength){
				$("#pd-nt").addClass("dis");
			}
			$("#pd-imgCnt").html((curImgNo+1) + " of " + totalLength);
		}
	}
}
function getGist(e){
	 var gistDiv = $("#sc-gist");
	 if(gistDiv.is(":visible")){//If gist is already shown, dont do anything
	 	return false;
	 }
	 var urlPattern = /\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/gim;

    // www. sans http:// or https://
    var pseudoUrlPattern = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    
	var urls = [], post = $("#startChat").val().trim();	
    
	var matches = post.match(urlPattern);
	if(matches)
		urls = urls.concat(matches); 
	
	matches = post.match(pseudoUrlPattern);
	if(matches)
		urls = urls.concat(matches); 
	
	var curUrl = "", gistData = {};
	if(urls.length < 1){//If no urls in post content
		return false;
	}else{
		for(temp in urls){
			var tempUrl = urls[temp].trim();
			if(isValidUrl(tempUrl)){
				curUrl = tempUrl;
				break;
			}
		}
		if(!curUrl){
			return false;
		}
		gistData = gistDiv.data("gist") || {};
		if(gistData && gistData[curUrl]){//If already is got data
			buildGistContent(gistData[curUrl]);
			return false;
		}
	}
	$("#throbber").show();
	var postIframe = $("#postIframe"), ifSrc = 'parse.php?url=' + curUrl;
	if(postIframe.length < 1){
        $(document.body).append('<iframe id="postIframe" rel="nofollow" height="1" width="1" src="' + ifSrc + '"></iframe>');
	}else{
		postIframe.attr("src", ifSrc);
	}
	
}
function buildExtraImages(data){
	$("#imgThrobber").remove();
	if(!data.isSuccess){
		return false;
	}
	var resp = data.data; 
	addURLImages(resp);
	
}
function addURLImages(imgsArr){
	for (temp in imgsArr) {
		$("#pd-imgs").append('<img src="' + imgsArr[temp] + '"/>');
	}
	var totalImgLength = $("#pd-imgs img").length;
	$("#pd-imgCnt").html("1 of " + totalImgLength);
	$("#pd-pre").attr("class", "dis");
	if(totalImgLength > 1){
		$("#pd-nt").attr("class", "");
		$("#sc-gist .pd-tn").show();
		$("#sc-gist .pd-np").show();
	}else{
		$("#pd-nt").attr("class", "dis");
		
		if (totalImgLength == 0) {
			$("#sc-gist .pd-np").hide();
			$("#sc-gist .pd-tn").hide();
		}
	}
}
function handleURLResponse(data){
	if(!data.isSuccess){
		return false;
	}
	if (data.data && data.data.length > 0) {
		var gistDiv = $("#sc-gist"), curUrl = data.url;
		var gistData = gistDiv.data("gist") || {};
		gistData[curUrl] = data.data[0];
		gistDiv.data("gist", gistData);
		buildGistContent(gistData[curUrl]);
	}
}
function buildGistContent(resp){
	$("#pd-imgs").html("");
	var imagesArr = [];
	for (temp in resp.site_image) {
		for (ind in resp.site_image[temp]) {
			imagesArr.push(resp.site_image[temp][ind]);
		}
	}

	addURLImages(imagesArr);
	$("#pd-imgs").append('<div id="imgThrobber"><div></div></div>');
	$("#imgThrobber").show();
	
	var totalImgLength = $("#pd-imgs img").length;
	$("#pd-imgCnt").html("1 of " + totalImgLength);
	$("#pd-pre").attr("class", "dis");
	if(totalImgLength > 1){
		$("#pd-nt").attr("class", "");
		$("#sc-gist .pd-tn").show();
		$("#sc-gist .pd-np").show();
	}else{
		$("#pd-nt").attr("class", "dis");
		
		if(totalImgLength == 0){
			$("#sc-gist .pd-tn").hide();
			$("#sc-gist .pd-np").hide();
		}
	}
	
	if(resp.title)
		$("#pd-title h4").html((resp.title.og || resp.title.meta));
	else $("#pd-title h4").html("");
	
	$("#pd-url").html((resp.site_url || ""));
	
	if(resp.description)
		$("#pd-desc").html((resp.description.og || resp.description.meta));
	else $("#pd-desc").html("");
	
	$("#sc-gist").show();
	$("#throbber").hide();
}
function isValidUrl(url){
	return (/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(url) || /(^|[^\/])(www\.[\S]+(\b|$))/gim.test(url));
}