(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-640bd0e5"],{"0323":function(t,e,i){},"0599":function(t,e,i){},"0c1c":function(t,e,i){"use strict";i.r(e);var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return t.List?i("div",{staticStyle:{width:"100%",height:"100%"}},[i("div",{staticClass:"top u-f-item u-f-jsb"},[i("div",{staticClass:"u-f-item"},[i("div",{staticClass:"inShow",on:{click:t.inShowClick}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/39.png",alt:""}})]),i("p",{staticClass:"topTitle"},[t._v(t._s(t.title))])]),i("div",[i("el-select",{attrs:{placeholder:t.$t("albumDetailsText5"),"popper-class":"select","popper-append-to-body":!1},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}},t._l(t.$t("AlbumTopOptions"),function(t){return i("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})}),1)],1)]),i("div",{staticClass:"list u-f"},[i("transition",{attrs:{name:"show"}},[t.inShow?i("div",{staticClass:"filtrate"},[t._l(t.$t("exploreList"),function(e,a){return"brandRole"!=t.$route.query.type?i("div",{key:a,staticClass:"filtrateItem cursor"},[i("div",{staticClass:" u-f-jsb u-f u-f-item",staticStyle:{"padding-bottom":"20px"},on:{click:function(i){return t.titleClick(a,e)}}},[i("p",{staticClass:"filtrateTitle"},[t._v(t._s(e.name))]),i("div",{staticClass:"icon"},[i("img",{staticClass:"img",class:{titleItemFlag:!e.falg},attrs:{src:"https://ec.wexiang.vip/source/img/36.png",alt:""}})])]),i("el-collapse-transition",[e.falg?i("div",t._l(e.list,function(e,a){return i("div",{key:a,staticClass:"Item u-f-jsb u-f u-f-item ",on:{click:function(i){return t.filtrateTitleTrumpetItemClick(e)}}},[i("p",{staticClass:"filtrateTitleTrumpet"},[t._v(t._s(e.name))]),i("div",{class:{itemIcon:!e.falg},staticStyle:{"margin-right":"16px"}},[e.falg?i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/37.png",alt:""}}):t._e()])])}),0):t._e()])],1):t._e()}),i("div",{staticClass:"filtrateItem cursor"},[i("div",{staticClass:" u-f-jsb u-f u-f-item",staticStyle:{"padding-bottom":"20px"},on:{click:function(e){t.priceFalg=!t.priceFalg}}},[i("p",{staticClass:"filtrateTitle"},[t._v(t._s(t.$t("albumDetailsText7")))]),i("div",{staticClass:"icon"},[i("img",{staticClass:"img",class:{titleItemFlag:!t.priceFalg},attrs:{src:"https://ec.wexiang.vip/source/img/36.png",alt:""}})])]),i("el-collapse-transition",[t.priceFalg?i("div",[i("div",[i("el-select",{staticStyle:{width:"100px","padding-left":"19px"},attrs:{placeholder:t.$t("albumDetailsText8"),"popper-class":"select","popper-append-to-body":!1},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}},t._l(t.$t("priceList"),function(t){return i("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})}),1)],1),i("div",{staticClass:"u-f u-f-item Price"},[i("div",{staticClass:"connomPrice"},[i("el-input",{staticStyle:{background:"#101320"},attrs:{placeholder:t.$t("albumDetailsText9")},on:{change:t.inpuTchange},model:{value:t.min_price,callback:function(e){t.min_price=e},expression:"min_price"}})],1),i("p",{staticClass:"PriceText"},[t._v("-")]),i("div",{staticClass:"connomPrice"},[i("el-input",{attrs:{placeholder:t.$t("albumDetailsText10")},on:{change:t.inpuTchange},model:{value:t.max_price,callback:function(e){t.max_price=e},expression:"max_price"}})],1)])]):t._e()])],1),"brandRole"==t.$route.query.type?i("div",[i("div",{staticClass:"filtrateItem",staticStyle:{"border-bottom":"none","padding-bottom":"15px"}},[i("div",{staticClass:" u-f-jsb u-f u-f-item",staticStyle:{"padding-bottom":"0px"}},[i("p",{staticClass:"filtrateTitle"},[t._v("属性")])])]),i("div",{staticClass:"filtrateItem cursor",staticStyle:{"padding-top":"0"}},t._l(t.roleList,function(e,a){return i("div",{key:a,staticStyle:{"margin-top":"20px"}},[i("div",{staticClass:" u-f-jsb u-f u-f-item",staticStyle:{"padding-bottom":"20px"},on:{click:function(i){return t.titleClick(a,e)}}},[i("p",{staticClass:"filtrateTitle",staticStyle:{color:"#6A78BA"}},[t._v(t._s(e.name))]),i("div",{staticClass:"icon"},[i("img",{staticClass:"img",class:{titleItemFlag:!e.falg},attrs:{src:"https://ec.wexiang.vip/source/img/36.png",alt:""}})])]),i("el-collapse-transition",[e.falg?i("div",t._l(e.list,function(e,a){return i("div",{key:a,staticClass:"Item u-f-jsb u-f u-f-item ",on:{click:function(i){return t.propertyClick(e)}}},[i("p",{staticClass:"filtrateTitleTrumpet"},[t._v(t._s(e.name))]),i("div",{class:{itemIcon:!e.falg},staticStyle:{"margin-right":"16px"}},[e.falg?i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/37.png",alt:""}}):t._e()])])}),0):t._e()])],1)}),0)]):t._e()],2):t._e()]),t.empty?i("div",{staticStyle:{width:"calc(100vw)",height:"100%"}},[i("empty")],1):i("div",{staticStyle:{height:"100%"}},[i("transition",{attrs:{name:"el-fade-in-linear"}},[t.List.length?i("div",{staticClass:"u-f",staticStyle:{height:"100%"}},[i("div",{staticClass:"letf",style:{width:"audio"==t.$route.query.type?"470px":""}},[i("transition",{attrs:{name:"el-fade-in-linear"}},[t.showFalg?i("div",[i("div",{staticClass:"letfImg"},["audio"!=t.$route.query.type?i("div",[i("el-image",{staticClass:"img",staticStyle:{"border-radius":"10px"},attrs:{src:"brandRole"!=t.$route.query.type?t.pitchItem.cover:t.pitchItem.image_url,fit:"cover"}},[i("div",{staticClass:"image-slot u-f-justify",attrs:{slot:"placeholder"},slot:"placeholder"},[i("loading")],1)])],1):i("div",{staticClass:"audioInfo"},[i("transition",{attrs:{name:"el-fade-in-linear"}},[t.audioShow?i("div",{staticClass:"audioInfoBgc",on:{mouseenter:function(e){t.audioInfoOperationShow=!0},mouseleave:function(e){t.audioInfoOperationShow=!1}}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img2/1.png",alt:""}}),i("div",{staticClass:"audioInfoImg"},[i("el-image",{staticClass:"img imageSpin",class:{pause:!t.pitchItem.falg},staticStyle:{"border-radius":"50%"},attrs:{src:t.pitchItem.cover,fit:"cover"}},[i("div",{staticClass:"audioInfoImg ilRbjP",attrs:{slot:"placeholder"},slot:"placeholder"})])],1),i("transition",{attrs:{name:"el-fade-in-linear"}},[t.audioInfoOperationShow?i("div",{staticClass:"audioInfoOperation u-f-justify cursor",on:{click:t.audioInfoOperationClick}},[t.pitchItem.falg?i("img",{staticStyle:{width:"46px",height:"53px"},attrs:{src:"https://ec.wexiang.vip/source/img2/6.png"}}):i("img",{staticStyle:{width:"49px",height:"54px"},attrs:{src:"https://ec.wexiang.vip/source/img2/5.png"}})]):t._e()]),i("div",{staticClass:"audioInfoControl"},[i("img",{staticClass:"img",class:{ControlSpin:t.pitchItem.falg},attrs:{src:"https://ec.wexiang.vip/source/img2/7.png"}})])],1):t._e()])],1)]),i("div",{staticClass:"u-f u-f-jsb u-f-item"},[i("p",{staticClass:"letfName singleLine"},[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(t.pitchItem.title)+" #"+t._s(t.pitchItem.code)+"\n\t\t\t\t\t\t\t\t\t")]),i("div",{staticClass:"u-f u-f-item praise",on:{click:t.zanClick}},[i("div",[t.pitchItem.is_care?i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/84.png",alt:""}}):i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/41.png",alt:""}})]),i("p",{staticClass:"praiseShu"},[t._v(t._s(t.pitchItem.likes))])])]),i("p",{staticClass:"series"},[t._v(t._s(t.pitchItem.cate_name))]),i("div",{staticClass:"describe"},[t._v("作者："+t._s(t.pitchItem.author))]),i("div",{staticClass:"describe",class:{describe1:"audio"==t.$route.query.type},domProps:{innerHTML:t._s(t.pitchItem.content)}}),"audio"!=t.$route.query.type?i("div",[t.pitchItem.labels?i("div",{staticClass:"u-f",staticStyle:{"margin-bottom":"60px"}},t._l(t.pitchItem.labels,function(e,a){return i("p",{key:a,staticClass:"typeCommon",style:{backgroundColor:e.bak_color}},[t._v(t._s(e.name))])}),0):t._e(),"game"==t.$route.query.type?i("div",t._l(t.pitchItem.game_version,function(e,a){return i("div",{staticClass:"u-f-item gameLink"},[i("p",{staticClass:"gameLinkName "},[t._v(t._s(e.version)+"版本链接")]),i("p",{staticClass:"gameLinkUrl cursor singleLine"},[t._v(t._s(e.url))])])}),0):t._e(),"colleagues"==t.$route.query.type?i("div",[i("div",{staticClass:"u-f colleaguesList  u-f-wrap"},t._l(t.pitchItem.chapter,function(e,a){return i("div",{key:a,staticClass:"colleaguesListItem cursor",on:{click:function(e){return t.sectionClick(a)}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(e.index)+"\n\t\t\t\t\t\t\t\t\t\t\t")])}),0)]):t._e()]):t._e()]):t._e()]),i("transition",{attrs:{name:"el-fade-in-linear"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:"audio"==t.$route.query.type&&t.showFalg&&t.pitchItemUrl==t.pitchItem.url,expression:"$route.query.type == 'audio'&& showFalg && pitchItemUrl == pitchItem.url"}]},[i("div",{staticClass:"audioStyle",class:{audioStyle1:t.pitchItem.content}},[i("audio-player",{ref:"audio",staticClass:"audio-box",attrs:{fileurl:t.pitchItemUrl,info:t.pitchItem},on:{playAudio:t.playAudio}})],1)])]),i("transition",{attrs:{name:"el-fade-in-linear"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:"audio"==t.$route.query.type&&t.showFalg&&t.pitchItemUrl!=t.pitchItem.url,expression:"$route.query.type == 'audio'&& showFalg && pitchItemUrl != pitchItem.url"}]},[i("div",{staticClass:"audioStyle",class:{audioStyle1:t.pitchItem.content}},[i("audio-player",{ref:"audio1",staticClass:"audio-box",attrs:{fileurl:t.pitchItem.url,info:t.pitchItem},on:{playAudio:t.playAudio}})],1)])])],1),i("div",{ref:"scroll",staticClass:"right ",style:{width:t.calculative},on:{scroll:t.scroll,mouseenter:t.mouseenterList,mouseleave:t.mouseleaveList}},["ikon"==t.$route.query.type?i("waterfall",{attrs:{col:5,data:t.List}},[t._l(t.List,function(e,a){return i("div",{key:a,staticClass:"item",class:{allItemPitch:t.pitchItem==e},staticStyle:{"margin-right":"10px"},on:{click:function(i){return t.itemClick(e)},mouseenter:function(i){return t.mouseenter(e)}}},[i("div",{staticClass:"itemImg"},[i("img",{staticClass:"img",attrs:{src:e.cover,alt:""}})]),i("div",{staticClass:"particulars"},[i("p",{staticClass:"itemName singleLine"},[t._v(t._s(e.title))]),i("div",{staticClass:"serial singleLine"},[t._v("#"+t._s(e.code))])])])})],2):i("div",{staticClass:"listAll u-f u-f-justify"},[t._l(t.List,function(e,a){return i("div",{key:a,staticClass:"allItem cursor",class:{allItemPitch:t.pitchItem==e},on:{click:function(i){return t.itemClick(e)},mouseenter:function(i){return t.mouseenter(e)}}},["audio"!=t.$route.query.type?i("div",{staticStyle:{height:"240px",margin:"10px 10px 16px"}},[i("el-image",{staticClass:"img",staticStyle:{"border-radius":"10px"},attrs:{src:"brandRole"!=t.$route.query.type?e.cover:e.cover+"?x-oss-process=image/crop,x_1900,y_1900,w_1200,h_1200/format,webp",fit:"cover"}},[i("div",{staticClass:"ilRbjP",staticStyle:{height:"240px",margin:"10px 10px 16px"},attrs:{slot:"placeholder"},slot:"placeholder"})])],1):i("div",{staticClass:"audioItem"},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img2/1.png",alt:""}}),i("div",{staticClass:"audioItemImg"},[i("el-image",{staticClass:"img",staticStyle:{"border-radius":"50%"},attrs:{src:e.cover,fit:"cover"}},[i("div",{staticClass:"audioItemImg ilRbjP",attrs:{slot:"placeholder"},slot:"placeholder"})])],1)]),i("div",{staticClass:"particulars"},[i("p",{staticClass:"itemName singleLine"},[t._v(t._s(e.title))]),i("div",{staticClass:"serial singleLine"},[t._v("#"+t._s(e.code))])])])}),t._l(6,function(t){return i("i")})],2)],1)]):t._e()]),t.List.length?t._e():i("div",{staticClass:"u-f-justify",staticStyle:{width:"calc(100vw)",height:"100%",position:"relative",top:"-100px"}},[i("loading")],1)],1)],1)]):t._e()},s=[],r=i("75fc"),o=(i("7f7f"),i("ac6a"),i("f3e2"),i("f8dc")),n=i("54a1"),c=i("b800");i("78ce");function l(t){var e,i=arguments.length>1&&void 0!==arguments[1]?arguments[1]:200;return function(){var a=this,s=arguments;e&&clearTimeout(e),e=setTimeout(function(){t.apply(a,s)},i)}}var u={components:{AudioPlayer:o["a"],loading:n["default"],empty:c["a"]},data:function(){return{inShow:!0,priceFalg:!0,list:[{name:"状态",falg:!0,list:[{name:"立即购买",falg:!1},{name:"拍卖中",falg:!1},{name:"新品",falg:!1},{name:"促销",falg:!1}]}],value:"",pitchItem:{},page:1,count:0,min_price:"",max_price:"",audioShow:!1,saveY:0,times:!1,audioInfoOperationShow:!1,showFalg:!1,pitchItemUrl:"",roleList:JSON.parse(localStorage.getItem("jshopconf")).role,empty:!1}},computed:{title:function(){return"colleagues"==this.$route.query.type?this.$t("exploreTitle4"):"game"==this.$route.query.type?this.$t("exploreTitle5"):"ikon"==this.$route.query.type?this.$t("exploreTitle2"):"brandRole"==this.$route.query.type?this.$t("exploreTitle1"):"audio"==this.$route.query.type?this.$t("exploreTitle3"):void 0},navigationFlag:function(){return this.$store.getters.navigationFlag},calculative:function(){return"calc(100% - 470px)"},List:function(){return"colleagues"==this.$route.query.type?this.$store.getters.exploreColleaguesList:"game"==this.$route.query.type?this.$store.getters.exploreGameList:"ikon"==this.$route.query.type?this.$store.getters.exploreIkonList:"brandRole"==this.$route.query.type?this.$store.getters.exploreBrandRoleList:"audio"==this.$route.query.type?this.$store.getters.exploreAudioList:void 0}},watch:{"$route.query.type":{handler:function(t,e){var i=this;"audio"==this.$route.query.type?this.$nextTick(function(){i.audioShow=!0}):this.audioShow=!1},deep:!0},"pitchItem.url":{handler:function(t,e){var i=this;this.showFalg=!1,setTimeout(function(){i.showFalg=!0},300)},deep:!0}},created:function(){this.getList()},activated:function(){var t=this;this.$nextTick(function(){t.List.length&&t.$refs.scroll.scrollTo(0,t.saveY,0)})},methods:{inShowClick:function(){this.inShow=!this.inShow},titleClick:function(t,e){e.falg=!e.falg},filtrateTitleTrumpetItemClick:function(t){t.falg=!t.falg},propertyClick:function(t){t.falg=!t.falg,this.page=1,this.$store.commit("list/onExplore".concat(this.$route.query.type),[]),this.getList()},inpuTchange:function(){this.page=1,this.$store.commit("list/onExplore".concat(this.$route.query.type),[]),this.getList()},getList:function(){var t=this,e={method:"",page:this.page,limit:30,min_price:this.min_price,max_price:this.max_price};"colleagues"==this.$route.query.type?(e.method="articles.getArticleList",e.type=0):"game"==this.$route.query.type?(e.method="notice.getgameList",e.type=0):"ikon"==this.$route.query.type?(e.method="images.getpaintings",e.is_market=0):"brandRole"==this.$route.query.type?(e.method="goods.getrolelist",e.type=0,e.role_type=[],e.figure=[],this.roleList.forEach(function(t,i){t.list.forEach(function(t){0==i?t.falg&&(console.log(t),e.role_type.push(t.name)):t.falg&&e.figure.push(t.name)})})):"audio"==this.$route.query.type&&(e.method="notice.audiolist",e.is_market=0),console.log(e),this.$get(e,!1).then(function(e){var i=e.data.data.list,a={cover:"",title:"",code:"",likes:"",cate_name:"",content:"",labels:""},s=[];i.forEach(function(e,i){"colleagues"==t.$route.query.type?(a=e,a.author=e.auother,a.content=e.brief,s.push(a)):"game"==t.$route.query.type?(a=e,a.title=e.game_name,a.content=e.game_content,a.author="平台",s.push(a)):"ikon"==t.$route.query.type?(a=e,a.title=e.name,a.content=e.desc,a.author=e.username,s.push(a)):"brandRole"==t.$route.query.type?(a=e,a.cover=e.image_url,a.title=e.name,a.code=e.bn,a.content=e.brief,a.author="平台",s.push(a)):"audio"==t.$route.query.type&&(a=e,a.cover=e.path,a.title=e.name,a.code=e.code,a.content=e.desc,a.cate_name=e.cat_name,a.url=e.url,a.falg=!1,s.push(a))}),t.count=e.data.data.count,t.empty=!t.count,t.$store.commit("list/onExplore".concat(t.$route.query.type),[].concat(Object(r["a"])(t.List),s)),"audio"==t.$route.query.type&&t.$nextTick(function(){t.audioShow=!0}),t.pitchItem=t.List[0],t.pitchItemUrl||(t.pitchItemUrl=t.List[0].url)})},itemClick:function(t){var e=t.id;"game"==this.$route.query.type&&(e=t.g_id),this.$router.push({path:"/explore/particulars",query:{id:e,type:this.$route.query.type}})},scroll:function(t){var e=t.target.scrollTop,i=t.target.clientHeight,a=t.target.scrollHeight;this.saveY=e,e+i+100>=a&&this.List.length<this.count&&(this.page++,this.getList())},zanClick:function(){var t=this;if(!localStorage.getItem("token"))return this.$store.commit("account/onRegister",!0);var e="",i=this.pitchItem.id;"colleagues"==this.$route.query.type?e=1:"game"==this.$route.query.type?(e=3,i=this.pitchItem.g_id):"ikon"==this.$route.query.type?e=4:"brandRole"==this.$route.query.type?e=5:"audio"==this.$route.query.type&&(e=2);var a=this.pitchItem.is_care;this.$post({id:i,type:e,method:"user.usercare",act:a?2:1}).then(function(e){e.data.status?t.pitchItem.id&&(t.pitchItem.is_care?t.pitchItem.likes--:t.pitchItem.likes++,t.pitchItem.is_care=!t.pitchItem.is_care):t.$message.error(e.data.msg)})},audioInfoOperationClick:function(){var t=this;this.pitchItem.lock_info.is_lock?this.$message.error("该内容为付费，请先解锁内容！"):(this.pitchItemUrl=this.pitchItem.url,setTimeout(function(){t.List.forEach(function(e){e.id==t.pitchItem.id?e.falg=!e.falg:e.falg=!1}),t.$refs.audio.playAudio(!0)}))},playAudio:function(){var t=this;this.pitchItemUrl=this.pitchItem.url,setTimeout(function(){t.List.forEach(function(e){e.id==t.pitchItem.id?e.falg=!e.falg:e.falg=!1})})},mouseenterList:function(){this.times=!0},mouseleaveList:function(){this.times=!1},mouseenter:l(function(t){this.times&&("play"==this.$refs.audio.$data.audioStatus&&(this.pitchItemUrl=t.url),this.pitchItem=t)},300),sectionClick:function(t){}}},p=u,d=(i("4b1f"),i("f204"),i("3e39"),i("2877")),m=Object(d["a"])(p,a,s,!1,null,"6811c2ca",null);e["default"]=m.exports},"1af6":function(t,e,i){var a=i("63b6");a(a.S,"Array",{isArray:i("9003")})},"20fd":function(t,e,i){"use strict";var a=i("d9f6"),s=i("aebd");t.exports=function(t,e,i){e in t?a.f(t,e,s(0,i)):t[e]=i}},"3e39":function(t,e,i){"use strict";var a=i("0599"),s=i.n(a);s.a},"4b1f":function(t,e,i){"use strict";var a=i("c3b4"),s=i.n(a);s.a},"549b":function(t,e,i){"use strict";var a=i("d864"),s=i("63b6"),r=i("241e"),o=i("b0dc"),n=i("3702"),c=i("b447"),l=i("20fd"),u=i("7cd6");s(s.S+s.F*!i("4ee1")(function(t){Array.from(t)}),"Array",{from:function(t){var e,i,s,p,d=r(t),m="function"==typeof this?this:Array,h=arguments.length,f=h>1?arguments[1]:void 0,g=void 0!==f,v=0,y=u(d);if(g&&(f=a(f,h>2?arguments[2]:void 0,2)),void 0==y||m==Array&&n(y))for(e=c(d.length),i=new m(e);e>v;v++)l(i,v,g?f(d[v],v):d[v]);else for(p=y.call(d),i=new m;!(s=p.next()).done;v++)l(i,v,g?o(p,f,[s.value,v],!0):s.value);return i.length=v,i}})},"54a16":function(t,e,i){i("6c1c"),i("1654"),t.exports=i("95d5")},6788:function(t,e,i){"use strict";var a=i("0323"),s=i.n(a);s.a},"75fc":function(t,e,i){"use strict";var a=i("a745"),s=i.n(a);function r(t){if(s()(t)){for(var e=0,i=new Array(t.length);e<t.length;e++)i[e]=t[e];return i}}var o=i("774e"),n=i.n(o),c=i("c8bb"),l=i.n(c);function u(t){if(l()(Object(t))||"[object Arguments]"===Object.prototype.toString.call(t))return n()(t)}function p(){throw new TypeError("Invalid attempt to spread non-iterable instance")}function d(t){return r(t)||u(t)||p()}i.d(e,"a",function(){return d})},"76ca":function(t,e,i){"use strict";var a=i("90ce"),s=i.n(a);s.a},"774e":function(t,e,i){t.exports=i("d2d5")},"80d3":function(t,e,i){},"88fd":function(t,e,i){"use strict";var a=i("dc02"),s=i.n(a);s.a},"90ce":function(t,e,i){},"95d5":function(t,e,i){var a=i("40c3"),s=i("5168")("iterator"),r=i("481b");t.exports=i("584a").isIterable=function(t){var e=Object(t);return void 0!==e[s]||"@@iterator"in e||r.hasOwnProperty(a(e))}},a745:function(t,e,i){t.exports=i("f410")},b800:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"u-f-item u-f-column"},[i("div",{staticClass:"emptyImg"},[i("el-image",{staticClass:"img",attrs:{src:t.img,fit:"contain"}})],1)])},s=[],r={data:function(){return{img:i("ba12")}}},o=r,n=(i("76ca"),i("2877")),c=Object(n["a"])(o,a,s,!1,null,"0a3f3e12",null);e["a"]=c.exports},ba12:function(t,e,i){t.exports=i.p+"src/img/15.c0c84ca2.png"},c3b4:function(t,e,i){},c8bb:function(t,e,i){t.exports=i("54a16")},d2d5:function(t,e,i){i("1654"),i("549b"),t.exports=i("584a").Array.from},dc02:function(t,e,i){},f204:function(t,e,i){"use strict";var a=i("80d3"),s=i.n(a);s.a},f410:function(t,e,i){i("1af6"),t.exports=i("584a").Array.isArray},f8dc:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("audio",{ref:"audioRef",staticStyle:{display:"none"},attrs:{src:t.fileurl,controls:""},on:{timeupdate:t.updateProgress}},[i("source",{attrs:{src:t.fileurl,type:"audio/mpeg"}}),t._v("\n\t\t\t您的浏览器不支持音频播放\n\t\t")]),i("div",{staticClass:"audio-right"},[i("div",[i("span",{staticClass:"audio-length-current",attrs:{id:"audioCurTime"}},[t._v(t._s(t.audioStart))])]),i("div",{directives:[{name:"dragto",rawName:"v-dragto",value:t.setAudioIcon,expression:"setAudioIcon"}],staticClass:"progress-bar-bg",attrs:{id:"progressBarBg"}},[i("div",{staticClass:"progress-bar",attrs:{id:"progressBar"}})]),i("div",{staticClass:"audio-time",staticStyle:{"min-height":"10px"}},[i("span",{staticClass:"audio-length-total"},[t._v(t._s(t.duration))])]),i("div",{staticClass:"yuan u-f-justify",on:{click:function(e){return t.playAudio(!1)}}},["pause"==t.audioStatus?i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/81.png",alt:""}}):i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/80.png",alt:""}})]),i("div",{staticClass:"volume"},[i("div",{directives:[{name:"show",rawName:"v-show",value:t.audioHuds,expression:"audioHuds"}],staticClass:"volume-progress",on:{click:function(t){return t.stopPropagation(),function(){return!1}()}}},[i("div",{directives:[{name:"adjuster",rawName:"v-adjuster",value:t.handleShowMuteIcon,expression:"handleShowMuteIcon"}],staticClass:"volume-bar-bg",attrs:{id:"volumeBarBg"}},[i("div",{staticClass:"volume-bar",attrs:{id:"volumeBar"}})])]),i("i",{staticClass:"iconfont pl-1",class:t.audioIcon,on:{click:function(e){e.stopPropagation(),t.audioHuds=!t.audioHuds}}})])])])},s=[],r={props:{fileurl:{type:String},info:{type:Object}},data:function(){return{audioStatus:"play",audioStart:"0:00",duration:"0:00",audioVolume:.5,audioHuds:!1}},watch:{fileurl:{handler:function(t,e){document.getElementById("progressBar").style.width="0%"},deep:!0}},directives:{dragto:{inserted:function(t,e,i){t.addEventListener("click",function(t){var a=document.getElementById("progressBarBg").clientWidth,s=i.context.$refs.audioRef,r=t.offsetX/a,o=100*r;document.getElementById("progressBar").style.width=o+"%",s.currentTime=s.duration*r,s.play(),e.value()},!1)}},adjuster:{inserted:function(t,e,i){t.addEventListener("click",function(t){var a=document.getElementById("volumeBarBg").clientHeight,s=i.context.$refs.audioRef,r=t.offsetY/a,o=100*r;document.getElementById("volumeBar").style.height=o+"%",s.volume=r,e.value(o/100)},!1)}}},computed:{audioIcon:function(){return this.audioHuds?this.audioVolume<.01?"checked icon-jingyin":"checked icon-shengyin":"icon-shengyin"}},mounted:function(){this.fetch()},methods:{fetch:function(){var t=this,e=this.$refs.audioRef;e.loop=!0,e.addEventListener("ended",function(){t.audioStatus="play",document.getElementById("progressBar").style.width="0%"},!1),null!=e&&(e.oncanplay=function(){t.duration=t.transTime(e.duration)},e.volume=.5)},playAudio:function(t){if(this.info.lock_info.is_lock)this.$message.error("该内容为付费，请先解锁内容！");else{var e=this.$refs.audioRef;if(e.paused?(e.play(),this.audioStatus="pause"):(e.pause(),this.audioStatus="play"),!t)return this.$emit("playAudio")}},updateProgress:function(t){var e=t.target.currentTime/t.target.duration;document.getElementById("progressBar")?(document.getElementById("progressBar").style.width=100*e+"%",t.target.currentTime===t.target.duration&&(this.audioStatus="pause")):this.audioStatus="pause",this.audioStart=this.transTime(this.$refs.audioRef.currentTime)},transTime:function(t){var e=parseInt(t),i=parseInt(e/60),a=e%60+"",s=":";return 0===i?i="00":i<10&&(i="0"+i),1===a.length&&(a="0"+a),i+s+a},setAudioIcon:function(){this.audioStatus="pause"},handleShowMuteIcon:function(t){this.audioVolume=t}}},o=r,n=(i("88fd"),i("6788"),i("2877")),c=Object(n["a"])(o,a,s,!1,null,"c3a9c534",null);e["a"]=c.exports}}]);