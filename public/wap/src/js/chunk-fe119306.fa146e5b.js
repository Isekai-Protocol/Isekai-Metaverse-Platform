(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-fe119306"],{"0233":function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{staticClass:"ikon"},[e("div",{staticClass:"title u-f u-f-jsb"},[e("div",{staticClass:"u-f-item"},[e("div",{staticClass:"u-f-item"},[t._m(0),e("p",{staticClass:"titleText"},[t._v(t._s(t.$t("ikon")))])]),e("div",{staticClass:"u-f u-f-item type"},t._l(t.$t("ikonList"),function(i,s){return e("p",{key:s,staticClass:"itemTitle",class:{itemTitlePitch:t.TitlePitch==s},on:{click:function(i){return t.titleClick(s)}}},[t._v(t._s(i))])}),0)]),e("div",{staticClass:"query cursor",on:{click:t.lookAll}},[t._v(t._s(t.$t("LookAll")))])]),e("transition",{attrs:{name:"el-fade-in-linear"}},[t.ikonList.length?e("div",{staticClass:"illustration"},[e("water-fall",{attrs:{data:t.ikonList}})],1):t._e()]),t.ikonList.length?t._e():e("loading")],1)},a=[function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/35.png",alt:""}})])}],n=e("54a1"),r=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("waterfall",{attrs:{col:t.col,data:t.data}},[t._l(t.data,function(i,s){return e("div",{key:s,staticClass:"cell-item",on:{click:function(e){return t.itemClick(i)}}},[e("img",{staticStyle:{"border-radius":"6px"},attrs:{src:i.cover,alt:"加载错误"}}),e("div",{staticClass:"item-body"},[e("div",{staticClass:"info"},[e("div",{staticClass:"item-desc line-clamp1 "},[t._v(t._s(i.name))]),e("div",{staticClass:"item-footer"},[e("div",{staticClass:"footer-left"},[e("img",{attrs:{src:i.avatar,alt:"",srcset:""}}),e("div",{staticClass:"name"},[t._v(t._s(i.username))])]),e("div",{staticClass:"like",on:{click:function(e){return e.stopPropagation(),t.zanClick(i)}}},[e("div",[i.is_care?e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/84.png",alt:""}}):e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/41.png",alt:""}})]),e("div",{staticClass:"like-total"},[t._v(t._s(i.likes))])])])])])])})],2)],1)},c=[],l={props:{data:{type:Array,default:function(){return[]}}},data:function(){return{col:12}},methods:{itemClick:function(t){this.$router.push({path:"/explore/particulars",query:{id:t.id,type:"ikon"}})},zanClick:function(t){if(!localStorage.getItem("token"))return this.$store.commit("account/onRegister",!0);var i=t.is_care;this.$post({id:t.id,type:4,method:"user.usercare",act:i?2:1}).then(function(i){t.is_care?(t.likes--,t.is_care=!1):(t.likes++,t.is_care=!0)})}}},o=l,u=(e("e14a"),e("2877")),p=Object(u["a"])(o,r,c,!1,null,"2a2f7154",null),d=p.exports,f={props:{ikonList:{type:Array,default:function(){return[]}}},components:{WaterFall:d,loading:n["default"]},data:function(){return{TitlePitch:0}},created:function(){},methods:{titleClick:function(t){this.TitlePitch=t,this.$emit("titleClick",t+1)},lookAll:function(){this.$router.push({path:"/explore?type=ikon"})}}},m=f,g=(e("1374"),Object(u["a"])(m,s,a,!1,null,"8f3b1094",null));i["a"]=g.exports},"07bf":function(t,i,e){},1374:function(t,i,e){"use strict";var s=e("1a31"),a=e.n(s);a.a},"1a31":function(t,i,e){},"1af6":function(t,i,e){var s=e("63b6");s(s.S,"Array",{isArray:e("9003")})},"20fd":function(t,i,e){"use strict";var s=e("d9f6"),a=e("aebd");t.exports=function(t,i,e){i in t?s.f(t,i,a(0,e)):t[i]=e}},2447:function(t,i,e){},2667:function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"selectAlbum"},[s("div",{staticClass:"title u-f u-f-jsb"},[s("div",{staticClass:"u-f-item"},[t._m(0),s("p",{staticClass:"titleText"},[t._v(t._s(t.$t("selectAlbum")))])]),s("div",{staticClass:"query cursor",on:{click:t.skipClick}},[t._v(t._s(t.$t("LookAll")))])]),s("transition",{attrs:{name:"el-fade-in-linear"}},[t.selectAlbumList.length?s("div",[t.reachEndFalgLfet?s("div",{staticClass:"shadowLfet"}):t._e(),s("div",{staticClass:"list u-f"},[s("swiper",{ref:"swiper",attrs:{options:t.swiperOption,autoplay:!0},on:{reachEnd:t.reachEnd,slideChangeTransitionStart:t.slideChangeTransitionStart}},[t._l(t.selectAlbumList,function(i,a){return s("swiper-slide",{key:a},[s("div",{staticClass:"item",on:{click:function(e){return t.itemCLikc(i)}}},[s("div",{staticClass:"itemPo"}),s("div",{staticClass:"itemCover"},[s("el-image",{staticClass:"img",attrs:{src:i.cover,fit:"cover"}},[s("div",{staticClass:"image-slot u-f-justify",attrs:{slot:"placeholder"},slot:"placeholder"},[s("img",{attrs:{src:e("90b1"),alt:""}})])])],1),s("div",{staticClass:"information u-f-item u-f-column"},[s("div",{staticClass:"hedaImg u-f-justify"},[s("el-image",{staticClass:"image",attrs:{src:i.headimg,fit:"cover"}},[s("div",{staticClass:"image-slot u-f-justify",attrs:{slot:"placeholder"},slot:"placeholder"},[s("img",{attrs:{src:e("90b1"),alt:""}})])])],1),s("p",{staticClass:"name"},[t._v(t._s(i.type_name))]),s("p",{staticClass:"introduce"},[t._v(t._s(i.user_name))]),s("p",{staticClass:"text",domProps:{innerHTML:t._s(i.desc)}})])])])}),s("div",{staticClass:"swiper-button-prev",attrs:{slot:"button-prev"},slot:"button-prev"}),s("div",{staticClass:"swiper-button-next",attrs:{slot:"button-next"},slot:"button-next"})],2)],1),t.reachEndFalg?s("div",{staticClass:"shadow"}):t._e()]):t._e()]),t.selectAlbumList.length?t._e():s("loading")],1)},a=[function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/11.png",alt:""}})])}],n=e("7212"),r=(e("dfa4"),e("54a1")),c={props:{selectAlbumList:{type:Array,default:function(){return[]}}},components:{swiper:n["swiper"],swiperSlide:n["swiperSlide"],loading:r["default"]},data:function(){return{falg:!1,reachEndFalg:!0,reachEndFalgLfet:!1,activeIndex:0,swiperOption:{slidesPerView:"auto",grabCursor:!0,speed:2e3,autoplay:{delay:5e3,stopOnLastSlide:!1,disableOnInteraction:!1},navigation:{nextEl:".swiper-button-next",prevEl:".swiper-button-prev"}}}},methods:{reachEnd:function(){this.reachEndFalg=!1},slideChangeTransitionStart:function(){this.reachEndFalg=!0,this.reachEndFalgLfet=!0,0==this.$refs.swiper.swiper.activeIndex&&(this.reachEndFalgLfet=!1)},skipClick:function(){this.$router.push({path:"/explore/album"})},itemCLikc:function(t){this.$router.push({path:"/explore/albumDetails",query:{id:t.id}})}}},l=c,o=(e("cfbe"),e("3133"),e("2877")),u=Object(o["a"])(l,s,a,!1,null,"0a0fd240",null);i["a"]=u.exports},3133:function(t,i,e){"use strict";var s=e("5b8d"),a=e.n(s);a.a},3675:function(t,i,e){"use strict";var s=e("805b"),a=e.n(s);a.a},"3a4e":function(t,i,e){"use strict";var s=e("2447"),a=e.n(s);a.a},"3ad9":function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("transition",{attrs:{name:"el-fade-in-linear"}},[t.thirdPartyList.length?e("div",{staticClass:"thirdPartyList u-f"},[t.reachEndFalgLfet.length?e("div",{staticClass:"shadowLfet"}):t._e(),e("swiper",{ref:"swiper",attrs:{options:t.swiperOption},on:{reachEnd:t.reachEnd,slideChangeTransitionStart:t.slideChangeTransitionStart}},[t._l(t.thirdPartyList,function(i,s){return e("swiper-slide",{key:s},[e("div",{staticClass:"thirdPartyItem",on:{mouseenter:function(e){return t.mouseenter(i)},mouseout:t.mouseout,click:function(e){return t.itemClick(i)}}},[e("el-image",{staticClass:"img thirdPartyItemImg",class:{thirdPartyItemImgPitch:t.pitchItem==i},attrs:{src:i.img,fit:"cover"}}),e("p",{staticClass:"thirdPartyItemName"},[t._v(t._s(i.name_china))])],1)])}),e("div",{staticClass:"swiper-button-prev",attrs:{slot:"button-prev"},slot:"button-prev"}),e("div",{staticClass:"swiper-button-next",attrs:{slot:"button-next"},slot:"button-next"})],2),t.reachEndFalg?e("div",{staticClass:"shadow"}):t._e()],1):t._e()]),t.thirdPartyList.length?t._e():e("loading")],1)},a=[],n=e("7212"),r=(e("dfa4"),e("54a1")),c={components:{swiper:n["swiper"],swiperSlide:n["swiperSlide"],loading:r["default"]},props:{thirdPartyList:{type:Array,default:function(){return[]}}},data:function(){return{activeIndex:0,reachEndFalg:!0,reachEndFalgLfet:!1,falg:!1,swiperOption:{slidesPerView:"auto",grabCursor:!0,speed:2e3,autoplay:{delay:5e3,stopOnLastSlide:!1,disableOnInteraction:!1},navigation:{nextEl:".swiper-button-next",prevEl:".swiper-button-prev"}},pitchItem:""}},methods:{reachEnd:function(){this.reachEndFalg=!1},slideChangeTransitionStart:function(){this.reachEndFalg=!0,this.reachEndFalgLfet=!0,0==this.$refs.swiper.swiper.activeIndex&&(this.reachEndFalgLfet=!1)},mouseenter:function(t){this.pitchItem=t},mouseout:function(){this.pitchItem=""},itemClick:function(t){window.open(t.val)}}},l=c,o=(e("7e67"),e("76fc"),e("2877")),u=Object(o["a"])(l,s,a,!1,null,"52dbb3fc",null);i["a"]=u.exports},5268:function(t,i,e){"use strict";var s=e("a7e9"),a=e.n(s);a.a},"540b":function(t,i,e){},"549b":function(t,i,e){"use strict";var s=e("d864"),a=e("63b6"),n=e("241e"),r=e("b0dc"),c=e("3702"),l=e("b447"),o=e("20fd"),u=e("7cd6");a(a.S+a.F*!e("4ee1")(function(t){Array.from(t)}),"Array",{from:function(t){var i,e,a,p,d=n(t),f="function"==typeof this?this:Array,m=arguments.length,g=m>1?arguments[1]:void 0,v=void 0!==g,h=0,_=u(d);if(v&&(g=s(g,m>2?arguments[2]:void 0,2)),void 0==_||f==Array&&c(_))for(i=l(d.length),e=new f(i);i>h;h++)o(e,h,v?g(d[h],h):d[h]);else for(p=_.call(d),e=new f;!(a=p.next()).done;h++)o(e,h,v?r(p,g,[a.value,h],!0):a.value);return e.length=h,e}})},"54a16":function(t,i,e){e("6c1c"),e("1654"),t.exports=e("95d5")},"565e":function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,s=t._self._c||i;return s("div",{staticClass:"frequency"},[s("div",{staticClass:"title u-f u-f-jsb"},[s("div",{staticClass:"u-f-item"},[t._m(0),s("p",{staticClass:"titleText"},[t._v(t._s(t.$t("frequency")))])]),s("div",{staticClass:"query cursor",on:{click:t.lookAll}},[t._v(t._s(t.$t("LookAll")))])]),s("transition",{attrs:{name:"el-fade-in-linear"}},[t.frequencyList.length?s("div",{staticClass:"list u-f u-f-jsb"},[s("div",{staticClass:"note"},[s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/17.png",alt:""}})]),t._l(t.frequencyList,function(i,a){return s("div",{key:a,staticClass:"item",class:{itemPitch:t.current==i.url},on:{mouseenter:function(e){return t.mouseenter(i)},mouseleave:t.mouseleave,click:function(e){return t.skip(i)}}},[s("div",{staticClass:"yuan u-f-justify u-f-item"},[s("div",{staticClass:"yuanBgc"},[s("img",{staticClass:"img",attrs:{src:e("89c2"),alt:""}})]),s("div",{staticClass:"yuanImg"},[s("el-image",{staticClass:"image",class:{imageSpin:t.current==i.url},attrs:{src:i.path,fit:"cover"}})],1),t.pitchItem==i.url||t.current==i.url?s("div",{staticClass:"operation u-f-justify u-f-item",on:{click:function(e){return e.stopPropagation(),t.itemCLick(i)}}},[t.current!=i.url?s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/19.png",alt:""}}):t._e(),t.current==i.url?s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/21.png",alt:""}}):t._e()]):t._e()]),s("p",{staticClass:"noteName singleLine"},[t._v(t._s(i.name))]),s("p",{staticClass:"describe singleLine"},[t._v(t._s(i.desc))]),s("div",{staticClass:"noteFrequency"},[t.current!=i.url?s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/20.png",alt:""}}):s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/79.gif",alt:""}})])])}),t._l(12,function(t){return s("i")})],2):t._e()]),t.frequencyList.length?t._e():s("loading"),s("transition",{attrs:{name:"el-fade-in-linear"}},[t.frequencyList1.length?s("div",{staticClass:"list u-f u-f-jsb"},[s("div",{staticClass:"note"},[s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/22.png",alt:""}})]),t._l(t.frequencyList1,function(i,a){return s("div",{key:a,staticClass:"item",class:{itemPitch:t.current==i.url},on:{mouseenter:function(e){return t.mouseenter(i)},mouseleave:t.mouseleave,click:function(e){return t.skip(i)}}},[s("div",{staticClass:"yuan u-f-justify u-f-item"},[s("div",{staticClass:"yuanBgc"},[s("img",{staticClass:"img",attrs:{src:e("89c2"),alt:""}})]),s("div",{staticClass:"yuanImg "},[s("el-image",{staticClass:"image",class:{imageSpin:t.current==i.url},attrs:{src:i.path,fit:"cover"}})],1),t.pitchItem==i.url||t.current==i.url?s("div",{staticClass:"operation u-f-justify u-f-item",on:{click:function(e){return e.stopPropagation(),t.itemCLick(i)}}},[t.current!=i.url?s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/19.png",alt:""}}):t._e(),t.current==i.url?s("img",{attrs:{src:"https://ec.wexiang.vip/source/img/21.png",alt:""}}):t._e()]):t._e()]),s("p",{staticClass:"noteName singleLine"},[t._v(t._s(i.name))]),s("p",{staticClass:"describe singleLine"},[t._v(t._s(i.desc))]),s("div",{staticClass:"noteFrequency"},[t.current!=i.url?s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/20.png",alt:""}}):s("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/79.gif",alt:""}})])])}),t._l(12,function(t){return s("i")})],2):t._e()]),t.frequencyList1.length?t._e():s("loading")],1)},a=[function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/16.png",alt:""}})])}],n=e("54a1"),r={components:{loading:n["default"]},props:{frequencyList:{type:Array,default:function(){return[]}},frequencyList1:{type:Array,default:function(){return[]}}},data:function(){return{screenWidth:null,screenHeight:null,current:"",pitchItem:""}},methods:{itemCLick:function(t){this.current!=t.url?(this.$emit("music",t.url),this.current=t.url):(this.$emit("stopMusic",t.url),this.current="")},mouseenter:function(t){this.pitchItem=t.url},mouseleave:function(){this.pitchItem=""},skip:function(t){this.$router.push({path:"/explore/particulars",query:{id:t.id,type:"audio"}})},lookAll:function(){this.$router.push({path:"/explore?type=audio"})}},computed:{CurrentScreenSize:function(){return this.$store.getters.CurrentScreenSize}}},c=r,l=(e("3a4e"),e("2877")),o=Object(l["a"])(c,s,a,!1,null,"76bf75af",null);i["a"]=o.exports},"5b7c":function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,e=t._self._c||i;return t.slideshowList.length?e("div",{staticClass:"slideshow"},[e("swiper",{ref:"swiper",attrs:{options:t.swiperOption},on:{slideChangeTransitionStart:t.transitionEnd}},[t._l(t.slideshowList,function(i,s){return e("swiper-slide",{key:s,staticClass:"show"},[e("div",{staticStyle:{width:"100%",height:"100%"},on:{click:function(e){return t.itemClick(i)}}},[e("img",{staticClass:"img",staticStyle:{"object-fit":"cover"},attrs:{src:i.img,alt:""}})])])}),e("div",{staticClass:"swiper-pagination"})],2)],1):t._e()},a=[],n=e("7212"),r=(e("dfa4"),{components:{swiper:n["swiper"],swiperSlide:n["swiperSlide"]},props:{slideshowList:{type:Array,default:function(){return[]}}},data:function(){return{swiperOption:{effect:"coverflow",speed:2e3,slidesPerView:1.4,loopAdditionalSlides:4,loop:!0,autoplay:{delay:5e3,disableOnInteraction:!1},loopFillGroupWithBlank:!0,coverflowEffect:{rotate:180,stretch:-170,depth:200,modifier:.4,slideShadows:!0},centeredSlides:!0},active:0,activeId:null}},created:function(){},methods:{transitionEnd:function(){this.$emit("folioItemClick",this.$refs.swiper.swiper.realIndex)},itemClick:function(t){this.$emit("itemClick",t)}}}),c=r,l=(e("9e4b"),e("2877")),o=Object(l["a"])(c,s,a,!1,null,"b420f0da",null);i["a"]=o.exports},"5b8d":function(t,i,e){},"72f5":function(t,i,e){},"75fc":function(t,i,e){"use strict";var s=e("a745"),a=e.n(s);function n(t){if(a()(t)){for(var i=0,e=new Array(t.length);i<t.length;i++)e[i]=t[i];return e}}var r=e("774e"),c=e.n(r),l=e("c8bb"),o=e.n(l);function u(t){if(o()(Object(t))||"[object Arguments]"===Object.prototype.toString.call(t))return c()(t)}function p(){throw new TypeError("Invalid attempt to spread non-iterable instance")}function d(t){return n(t)||u(t)||p()}e.d(i,"a",function(){return d})},"76fc":function(t,i,e){"use strict";var s=e("540b"),a=e.n(s);a.a},"774e":function(t,i,e){t.exports=e("d2d5")},"7e67":function(t,i,e){"use strict";var s=e("bd26"),a=e.n(s);a.a},"805b":function(t,i,e){},8794:function(t,i,e){"use strict";var s=e("bd4b"),a=e.n(s);a.a},"89c2":function(t,i,e){t.exports=e.p+"src/img/1.c6f7f40d.png"},"90b1":function(t,i,e){t.exports=e.p+"src/img/4.475bfcbb.gif"},9377:function(t,i,e){},"95d5":function(t,i,e){var s=e("40c3"),a=e("5168")("iterator"),n=e("481b");t.exports=e("584a").isIterable=function(t){var i=Object(t);return void 0!==i[a]||"@@iterator"in i||n.hasOwnProperty(s(i))}},"9e4b":function(t,i,e){"use strict";var s=e("07bf"),a=e.n(s);a.a},a745:function(t,i,e){t.exports=e("f410")},a7e9:function(t,i,e){},b64a:function(t,i,e){"use strict";var s=e("da28"),a=e.n(s);a.a},bd26:function(t,i,e){},bd4b:function(t,i,e){},c8bb:function(t,i,e){t.exports=e("54a16")},cf22:function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{staticClass:"recommended"},[e("div",{staticClass:"title u-f u-f-jsb"},[e("div",{staticClass:"u-f-item"},[t._m(0),e("p",{staticClass:"titleText"},[t._v(t._s(t.$t("recommended")))])])]),e("transition",{attrs:{name:"el-fade-in-linear"}},[t.recommendedList.length?e("div",[e("div",{staticClass:"authorList u-f"},[t.reachEndFalgLfet?e("div",{staticClass:"shadowLfet"}):t._e(),e("swiper",{ref:"swiper",attrs:{options:t.swiperOption},on:{reachEnd:t.reachEnd,slideChangeTransitionStart:t.slideChangeTransitionStart}},[t._l(t.recommendedList,function(i,s){return e("swiper-slide",{key:s},[e("div",{staticClass:"authorItem",on:{click:function(e){return t.itemCLick(i)}}},[e("el-image",{staticClass:"img",staticStyle:{"border-radius":"10px",filter:"blur(4px)"},attrs:{src:i.avatar,alt:"",fit:"none"}}),e("div",{staticStyle:{"background-color":"rgba(0,0,0,0.5)",width:"100%",height:"100%",position:"absolute",top:"0"}}),e("div",{staticClass:"head u-f-item u-f-column u-f-justify"},[e("div",{staticStyle:{width:"85px",height:"85px"}},[e("el-image",{staticClass:"image",attrs:{src:i.avatar,alt:"",fit:"cover"}},[e("div",{staticClass:"image-slot",attrs:{slot:"placeholder"},slot:"placeholder"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t加载中"),e("span",{staticClass:"dot"},[t._v("...")])])]),e("img")],1),e("p",{staticClass:"name"},[t._v(t._s(i.username))])])],1)])}),e("div",{staticClass:"swiper-button-prev",attrs:{slot:"button-prev"},slot:"button-prev"}),e("div",{staticClass:"swiper-button-next",attrs:{slot:"button-next"},slot:"button-next"})],2),t.reachEndFalg?e("div",{staticClass:"shadow"}):t._e()],1)]):t._e()]),t.recommendedList.length?t._e():e("loading")],1)},a=[function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/13.png",alt:""}})])}],n=e("7212"),r=(e("dfa4"),e("54a1")),c={components:{swiper:n["swiper"],swiperSlide:n["swiperSlide"],loading:r["default"]},props:{recommendedList:{type:Array,default:function(){return[]}}},data:function(){return{activeIndex:0,reachEndFalg:!0,reachEndFalgLfet:!1,falg:!1,falg1:!1,swiperOption:{slidesPerView:"auto",grabCursor:!0,speed:2e3,autoplay:{delay:5e3,stopOnLastSlide:!1,disableOnInteraction:!1},navigation:{nextEl:".swiper-button-next",prevEl:".swiper-button-prev"}}}},methods:{reachEnd:function(){this.reachEndFalg=!1},slideChangeTransitionStart:function(){this.reachEndFalg=!0,this.reachEndFalgLfet=!0,0==this.$refs.swiper.swiper.activeIndex&&(this.reachEndFalgLfet=!1)},itemCLick:function(t){var i=t.id;this.$router.push({path:"/userCenter",query:{id:i}})}}},l=c,o=(e("b64a"),e("3675"),e("2877")),u=Object(o["a"])(l,s,a,!1,null,"064e2cee",null);i["a"]=u.exports},cfbe:function(t,i,e){"use strict";var s=e("9377"),a=e.n(s);a.a},d018:function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{staticClass:"Article"},[e("div",{staticClass:"title u-f u-f-jsb"},[e("div",{staticClass:"u-f-item"},[t._m(0),e("p",{staticClass:"titleText "},[t._v(t._s(t.$t("Article")))])]),e("div",{staticClass:"query cursor",on:{click:t.lookAll}},[t._v(t._s(t.$t("LookAll")))])]),e("transition",{attrs:{name:"el-fade-in-linear"}},[t.articleList.length?e("div",[t.CurrentScreenSize>1800?e("div",{staticClass:"list u-f"},[e("div",{staticClass:"listLetf u-f"},[e("div",{staticClass:"letfImg"},[e("el-image",{staticStyle:{width:"100%",height:"100%","border-radius":"10px"},attrs:{src:t.pitchItem.cover,fit:"cover"}})],1),e("div",{staticClass:"describe u-f u-f-jsb  u-f-column",style:{width:t.navigationFlag?"470px":"580px"}},[e("div",[e("p",{staticClass:"itemTitle singleLine"},[t._v(t._s(t.pitchItem.title))]),e("p",{staticClass:"author singleLine"},[t._v(t._s(t.$t("ArticleText"))+"："+t._s(t.pitchItem.auother))]),e("div",{staticClass:"type"},t._l(t.pitchItem.labels,function(i,s){return e("span",{key:s,staticClass:"typeConnom",style:{backgroundColor:i.bak_color}},[t._v(t._s(i.name))])}),0),e("div",{staticClass:"itemContent",domProps:{innerHTML:t._s(t.pitchItem.content)}})]),e("div",{staticClass:"u-f textConnom"},[e("p",[t._v(t._s(t.$t("ArticleText1"))+t._s(t.pitchItem.chapter)+t._s(t.$t("ArticleText2")))]),e("p",{staticStyle:{margin:"0 30px 0 10px"}},[t._v(t._s(t.pitchItem.words)+t._s(t.$t("ArticleText3")))]),e("div",{staticClass:"cursor u-f",on:{click:function(i){return i.stopPropagation(),t.zanClick(t.pitchItem)}}},[e("div",[t.pitchItem.is_care?e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/84.png",alt:""}}):e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/41.png",alt:""}})]),e("p",{staticClass:"cursor",staticStyle:{"margin-left":"10px"}},[t._v(t._s(t.pitchItem.likes))])])])])]),e("div",{staticClass:"listRight u-f  u-f-column"},[e("div",{staticClass:"listRightList u-f u-f-jsb"},[t._l(t.articleList,function(i,s){return e("div",{key:s,staticClass:"listRightItem u-f-jsb u-f cursor",on:{mouseenter:function(e){return t.mouseenter(i)},click:function(e){return t.itemClick(i)}}},[e("div",{staticClass:"listRightItemImg"},[e("el-image",{staticStyle:{width:"100%",height:"100%","border-radius":"10px"},attrs:{src:i.cover,fit:"cover"}})],1),e("div",{staticClass:"listRightItemContent  u-f u-f-jsb  u-f-column",style:{width:t.navigationFlag?"150px":"170px"}},[e("div",[e("p",{staticClass:"itemTitle1 singleLine"},[t._v(t._s(i.title))]),e("p",{staticClass:"author1"},[t._v(t._s(t.$t("ArticleText"))+"："+t._s(i.auother))]),e("div",{staticClass:"type1"},t._l(i.labels,function(i,s){return e("span",{key:s,staticClass:"typeConnom1",style:{backgroundColor:i.bak_color}},[t._v(t._s(i.name))])}),0)]),e("div",{staticClass:"u-f u-f-jsb u-f-item textConnom1"},[e("div",[t._v(t._s(t.$t("ArticleText1"))+t._s(i.chapter)+t._s(t.$t("ArticleText2")))]),e("div",{staticClass:"u-f",on:{click:function(e){return e.stopPropagation(),t.zanClick(i)}}},[e("div",[i.is_care?e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/84.png",alt:""}}):e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/41.png",alt:""}})]),e("p",{staticStyle:{"margin-left":"10px"}},[t._v(t._s(i.likes))])])])])])}),t._l(6,function(t){return e("i")})],2)])]):e("div",{staticClass:"list"},[e("div",{staticClass:"listLetf u-f"},[e("div",{staticClass:"letfImg"},[e("el-image",{staticStyle:{width:"100%",height:"100%","border-radius":"10px"},attrs:{src:t.pitchItem.cover,fit:"cover"}})],1),e("div",{staticClass:"describe1 u-f u-f-jsb  u-f-column"},[e("div",[e("p",{staticClass:"itemTitle singleLine"},[t._v(t._s(t.pitchItem.title))]),e("p",{staticClass:"author singleLine"},[t._v(t._s(t.$t("ArticleText"))+"："+t._s(t.pitchItem.auother))]),e("div",{staticClass:"type"},t._l(t.pitchItem.labels,function(i,s){return e("span",{key:s,staticClass:"typeConnom",style:{backgroundColor:i.bak_color}},[t._v(t._s(i.name))])}),0),e("div",{staticClass:"itemContent",domProps:{innerHTML:t._s(t.pitchItem.content)}})]),e("div",{staticClass:"u-f textConnom"},[e("p",[t._v(t._s(t.$t("ArticleText1"))+t._s(t.pitchItem.chapter)+t._s(t.$t("ArticleText2")))]),e("p",{staticStyle:{margin:"0 30px 0 10px"}},[t._v(t._s(t.pitchItem.words)+t._s(t.$t("ArticleText3")))]),e("div",{staticClass:"cursor u-f",on:{click:function(i){return i.stopPropagation(),t.zanClick(t.pitchItem)}}},[e("p",[t.pitchItem.is_care?e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/84.png",alt:""}}):e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/41.png",alt:""}})]),e("p",{staticStyle:{"margin-left":"10px"}},[t._v(t._s(t.pitchItem.likes))])])])])]),e("div",{staticStyle:{"margin-top":"40px"}},[e("div",{staticClass:"listRightList u-f u-f-jsb"},t._l(t.articleList,function(i,s){return e("div",{key:s,staticClass:"listRightItem u-f-jsb u-f cursor",on:{mouseenter:function(e){return t.mouseenter(i)},click:function(e){return t.itemClick(i)}}},[e("div",{staticClass:"listRightItemImg"},[e("el-image",{staticStyle:{width:"100%",height:"100%","border-radius":"10px"},attrs:{src:i.cover,fit:"cover"}})],1),e("div",{staticClass:"listRightItemContent  u-f u-f-jsb  u-f-column",style:{width:t.navigationFlag?"120px":"170px"}},[e("div",[e("p",{staticClass:"itemTitle1 singleLine"},[t._v(t._s(i.title))]),e("p",{staticClass:"author1"},[t._v(t._s(t.$t("ArticleText"))+"："+t._s(i.auother))]),e("div",{staticClass:"type1"},t._l(i.labels,function(i,s){return e("span",{key:s,staticClass:"typeConnom1",style:{backgroundColor:i.bak_color}},[t._v(t._s(i.name))])}),0)]),e("div",{staticClass:"u-f u-f-jsb u-f-item textConnom1"},[e("div",[t._v(t._s(t.$t("ArticleText1"))+t._s(i.chapter)+t._s(t.$t("ArticleText2")))]),e("div",{staticClass:"u-f",on:{click:function(e){return e.stopPropagation(),t.zanClick(i)}}},[e("div",[i.is_care?e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/84.png",alt:""}}):e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/41.png",alt:""}})]),e("p",{staticStyle:{"margin-left":"10px"}},[t._v(t._s(i.likes))])])])])])}),0)])])]):t._e()]),t.articleList.length?t._e():e("loading")],1)},a=[function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/29.png",alt:""}})])}],n=e("54a1"),r={components:{loading:n["default"]},props:{articleList:{type:Array,default:function(){return[]}}},computed:{navigationFlag:function(){return this.$store.getters.navigationFlag},CurrentScreenSize:function(){return this.$store.getters.CurrentScreenSize}},data:function(){return{pitchItem:{}}},watch:{articleList:{handler:function(t,i){this.pitchItem=this.articleList[0]}}},methods:{mouseenter:function(t){this.pitchItem=t},itemClick:function(t){this.$router.push({path:"/explore/particulars",query:{id:t.id,type:"colleagues"}})},lookAll:function(){this.$router.push({path:"/explore?type=colleagues"})},zanClick:function(t){if(!localStorage.getItem("token"))return this.$store.commit("account/onRegister",!0);var i=t.is_care;this.$post({id:t.id,type:1,method:"user.usercare",act:i?2:1}).then(function(i){t.is_care?(t.likes--,t.is_care=!1):(t.likes++,t.is_care=!0)})}}},c=r,l=(e("8794"),e("2877")),o=Object(l["a"])(c,s,a,!1,null,"1859794e",null);i["a"]=o.exports},d2d5:function(t,i,e){e("1654"),e("549b"),t.exports=e("584a").Array.from},da28:function(t,i,e){},e14a:function(t,i,e){"use strict";var s=e("72f5"),a=e.n(s);a.a},f36e:function(t,i,e){"use strict";var s=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{staticClass:"game"},[e("div",{staticClass:"title u-f u-f-jsb"},[e("div",{staticClass:"u-f-item"},[t._m(0),e("p",{staticClass:"titleText"},[t._v(t._s(t.$t("game")))])]),e("div",{staticClass:"query cursor",on:{click:t.lookAll}},[t._v(t._s(t.$t("LookAll")))])]),e("transition",{attrs:{name:"el-fade-in-linear"}},[t.gameList.length?e("div",[e("div",{staticClass:"list u-f ",class:{"u-f-jsb":t.gameList.length>=6}},t._l(t.gameList,function(i,s){return e("div",{key:s,staticClass:"item u-f",style:{width:t.pitch==s?t.navigationFlag?"770px":"950px":"265px",marginRight:t.gameList.length<6?"20px":"0px"},on:{click:function(e){return t.itemClick(i)},mouseenter:function(i){t.pitch=s}}},[e("div",{staticClass:"itemImg"},[e("el-image",{staticClass:"img",attrs:{src:i.cover,fit:"cover"}})],1),t.pitch==s?e("div",{staticClass:"itemIntroduce  animate__animated animate__fadeIn"},[e("p",{staticClass:"itemName singleLine"},[t._v(t._s(t.gameList[t.pitch].game_name))]),e("p",{staticClass:"time singleLine"},[t._v("\n\t\t\t\t\t\t\t"+t._s(t.gameList[t.pitch].post_time)+t._s(t.gameList[t.pitch].post_addr)+"-"+t._s(t.gameList[t.pitch].game_cate)+"\n\t\t\t\t\t\t")]),e("div",{staticClass:"type"},t._l(i.labels,function(i,s){return e("span",{key:s,staticClass:"typeConnom",style:{backgroundColor:i.bak_color}},[t._v(t._s(i.name))])}),0),e("div",{staticClass:"itemText",domProps:{innerHTML:t._s(t.gameList[t.pitch].game_content)}}),e("div",{staticClass:"describe u-f u-f-jsb"},[e("div",{staticStyle:{width:"240px"}},[e("p",{staticClass:"singleLine"},[t._v("游戏类型："+t._s(t.gameList[t.pitch].game_type)+" ")]),e("p",{staticClass:"mt10 singleLine"},[t._v("游戏语言："+t._s(t.gameList[t.pitch].language))])]),e("div",{staticStyle:{width:"240px"}},[e("p",{staticClass:"singleLine"},[t._v("制作公司："+t._s(t.gameList[t.pitch].make_company)+" ")]),e("p",{staticClass:"mt10 singleLine"},[t._v("发行公司："+t._s(t.gameList[t.pitch].post_compay)+" ")])])])]):t._e()])}),0)]):t._e()]),t.gameList.length?t._e():e("loading")],1)},a=[function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",[e("img",{attrs:{src:"https://ec.wexiang.vip/source/img/31.png",alt:""}})])}],n=e("54a1"),r={components:{loading:n["default"]},props:{gameList:{type:Array,default:function(){return[]}}},data:function(){return{pitch:0}},computed:{navigationFlag:function(){return this.$store.getters.navigationFlag}},methods:{itemClick:function(t){this.$router.push({path:"/explore/particulars",query:{id:t.g_id,type:"game"}})},lookAll:function(){this.$router.push({path:"/explore?type=game"})}}},c=r,l=(e("5268"),e("2877")),o=Object(l["a"])(c,s,a,!1,null,"1f88d31e",null);i["a"]=o.exports},f410:function(t,i,e){e("1af6"),t.exports=e("584a").Array.isArray}}]);