!function(t){var e={};function o(a){if(e[a])return e[a].exports;var n=e[a]={i:a,l:!1,exports:{}};return t[a].call(n.exports,n,n.exports,o),n.l=!0,n.exports}o.m=t,o.c=e,o.d=function(t,e,a){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:a})},o.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var a=Object.create(null);if(o.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var n in t)o.d(a,n,function(e){return t[e]}.bind(null,n));return a},o.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="/",o(o.s=306)}({306:function(t,e,o){t.exports=o(307)},307:function(module,exports){jQuery(document).ready((function($){var token=$('meta[name="csrf-token"]'),header=$("#maia-header"),body=$("#maia-body"),hamburger=$('[aria-controls="navigation"]'),navigation=$("#navigation");body.find(".banner .bg").addClass("show"),$("*[data-inputmask]").each((function(){$(this).inputmask($(this).attr("data-inputmask"))})),$(".owl-carousel").each((function(){var attr=eval("("+$(this).attr("data-carousel")+")");if(attr.progress){var callback=function(t){$elem=t.target,buildProgressBar(),start()},buildProgressBar=function(){($progressBar=$("<div>",{id:"progressBar"})).addClass("progress"),($bar=$("<div>",{id:"bar"})).addClass("progress-bar").attr("role","progressbar").attr("aria-valuenow","0").attr("aria-valuemin","0").attr("aria-valuemax","100"),$progressBar.append($bar).prependTo($elem)},start=function(){percentTime=0,isPause=!1,tick=setInterval(interval,10)},interval=function(){!1===isPause&&(percentTime+=1/time,$bar.css({width:percentTime+"%"}).attr("aria-valuenow",percentTime),percentTime>=100&&$(this).trigger("next.owl.carousel"))},pauseOnDragging=function(){isPause=!0},moved=function(){clearTimeout(tick),start()},time=attr.autoplayTimeout?attr.autoplayTimeout/1e3-1:4,$progressBar,$bar,$elem,isPause,tick,percentTime;$(this).owlCarousel($.extend(attr,{onInitialized:callback,onTranslate:moved,onDrag:pauseOnDragging})),attr.autoplayHoverPause&&($(this).on("mouseover",(function(){isPause=!0})),$(this).on("mouseout",(function(){isPause=!1})))}else attr?$(this).owlCarousel(attr):$(this).owlCarousel()}));var scrollTrigger=100,Scroll=function(){$(window).scrollTop()>scrollTrigger?header.addClass("scrolling"):header.removeClass("scrolling")};function scrollWidth(){var t=document.createElement("div");t.style.overflowY="scroll",t.style.width="50px",t.style.height="50px",document.body.append(t);var e=t.offsetWidth-t.clientWidth;t.remove(),body.hasClass("open-menu")?(hamburger.css({marginRight:e+"px"}),body.css({paddingRight:e+"px"}),navigation.css({paddingRight:e+"px"})):(hamburger.removeAttr("style"),body.removeAttr("style"),navigation.removeAttr("style"))}function showNotification(t,e,o,a,n){$.notify({icon:e,message:o},{type:t,z_index:999999,newest_on_top:!0,delay:1500,timer:2500,placement:{from:a,align:n}})}function clearForm(t){t.find(".form-group").each((function(){$(this).find("input").val(""),$(this).find("textarea").val("")}))}function showNotify(){setTimeout((function(){$('[data-notify="container"]').each((function(){var t=$(this);t.addClass("show"),setTimeout((function(){t.removeClass("show")}),4e3),t.find('[data-notify="dismiss"]').on("click",(function(e){e.target;e.preventDefault(),t.removeClass("show")}))}))}),500)}Scroll(),$(window).on("scroll",(function(){Scroll(),$('.dropdown-toggle[aria-expanded="true"]').each((function(){$(this).dropdown("hide")}))})),hamburger.on("click",(function(){body.toggleClass("open-menu"),scrollWidth(),$(this).toggleClass("is-active"),$(this).parent().find("#"+$(this).attr("aria-controls")).toggleClass("hide")}));var contactForm=$("form.mainContactForm");contactForm.each((function(){var t=$(this);t.submit((function(e){e.preventDefault(),$.ajax({url:location.origin+"/ajax/contactForm",method:"POST",data:{_token:token.attr("content"),name:contactForm.find('input[name="name"]').val(),email:contactForm.find('input[name="email"]').val(),budget:contactForm.find('input[name="budget"]').val(),about:contactForm.find('textarea[name="about"]').val(),based:contactForm.find('input[name="based"]').val()},success:function(e){clearForm(t),showNotification("success","notifications",e.success,"top","center"),showNotify()},error:function(t){var e=t.responseJSON.errors;if("string"==typeof e)return showNotification("danger","notifications",e,"top","center");$.each(e,(function(t){$.each(e[t],(function(t,e){showNotification("danger","notifications",e,"top","center")}))})),showNotify()}})}))}))}))}});