(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-075d24ac"],{"1af6":function(t,e,i){var s=i("63b6");s(s.S,"Array",{isArray:i("9003")})},"20fd":function(t,e,i){"use strict";var s=i("d9f6"),a=i("aebd");t.exports=function(t,e,i){e in t?s.f(t,e,a(0,i)):t[e]=i}},"2fa7":function(t,e,i){},3980:function(t,e,i){},"3f87":function(t,e,i){"use strict";var s=i("cb93"),a=i.n(s);a.a},"493d":function(t,e,i){},"549b":function(t,e,i){"use strict";var s=i("d864"),a=i("63b6"),r=i("241e"),c=i("b0dc"),n=i("3702"),l=i("b447"),o=i("20fd"),u=i("7cd6");a(a.S+a.F*!i("4ee1")(function(t){Array.from(t)}),"Array",{from:function(t){var e,i,a,p,f=r(t),m="function"==typeof this?this:Array,d=arguments.length,g=d>1?arguments[1]:void 0,h=void 0!==g,v=0,b=u(f);if(h&&(g=s(g,d>2?arguments[2]:void 0,2)),void 0==b||m==Array&&n(b))for(e=l(f.length),i=new m(e);e>v;v++)o(i,v,h?g(f[v],v):f[v]);else for(p=b.call(f),i=new m;!(a=p.next()).done;v++)o(i,v,h?c(p,g,[a.value,v],!0):a.value);return i.length=v,i}})},"54a16":function(t,e,i){i("6c1c"),i("1654"),t.exports=i("95d5")},"75fc":function(t,e,i){"use strict";var s=i("a745"),a=i.n(s);function r(t){if(a()(t)){for(var e=0,i=new Array(t.length);e<t.length;e++)i[e]=t[e];return i}}var c=i("774e"),n=i.n(c),l=i("c8bb"),o=i.n(l);function u(t){if(o()(Object(t))||"[object Arguments]"===Object.prototype.toString.call(t))return n()(t)}function p(){throw new TypeError("Invalid attempt to spread non-iterable instance")}function f(t){return r(t)||u(t)||p()}i.d(e,"a",function(){return f})},"774e":function(t,e,i){t.exports=i("d2d5")},"905d":function(t,e,i){"use strict";var s=i("3980"),a=i.n(s);a.a},"95d5":function(t,e,i){var s=i("40c3"),a=i("5168")("iterator"),r=i("481b");t.exports=i("584a").isIterable=function(t){var e=Object(t);return void 0!==e[a]||"@@iterator"in e||r.hasOwnProperty(s(e))}},a5e4:function(t,e,i){"use strict";var s=i("2fa7"),a=i.n(s);a.a},a745:function(t,e,i){t.exports=i("f410")},c8bb:function(t,e,i){t.exports=i("54a16")},cb93:function(t,e,i){},ce102:function(t,e,i){"use strict";i.r(e);var s=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticStyle:{height:"calc(100vh - 70px)"}},[i("el-scrollbar",{ref:"scrollMenuRef",staticStyle:{height:"100%"},attrs:{wrapStyle:"overflow-x:hidden;"}},[t.userInfo.username?i("div",{staticClass:"wraper",class:{fixed:t.isfixed}},[t.userInfo.back_img?i("el-image",{staticClass:"header-back",style:{filter:t.filter},attrs:{src:t.userInfo.back_img,fit:"cover"}}):t._e(),i("div",{staticClass:"top-wrap"},[i("div",{staticClass:"user-info"},[i("el-image",{staticClass:"avatar",attrs:{src:t.userInfo.avatar,fit:"cover"}}),i("h3",{staticClass:"title"},[t._v(t._s(t.userInfo.username))]),i("h4",{staticClass:"bag"},[i("img",{attrs:{src:"https://ec.wexiang.vip/source/static/img/bag_icon.png"}}),t._v(t._s(t.userInfo.wallet_url))]),i("h4",{staticClass:"time"},[t._v(t._s(t.userInfo.ctime)+"加入")])],1)]),i("div",{staticClass:"body align-start",style:{height:t.isfixed?"calc(100vh - 70px)":"20000px"}},[i("div",{staticClass:"min-tabs flex"},[i("div",{staticClass:"big-small1 flex align-center"},[i("div",{staticClass:"big",style:{borderRadius:t.selfFlag?"":"8px"}},[i("div",[i("i",{staticClass:"el-icon-share"})])]),t.selfFlag?i("div",{staticClass:"small active cursor",on:{click:t.attentionClick}},[i("p",[t.userInfo.is_love?i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/84.png",alt:""}}):i("img",{attrs:{src:"https://ec.wexiang.vip/source/img/41.png",alt:""}})])]):t._e()]),i("transition",{attrs:{name:"el-fade-in-linear"}},[t.album?t._e():i("ul",{staticClass:"flex align-center justify-between"},t._l(t.navData,function(e,s){return i("li",{key:s,staticClass:"flex align-center justify-center cursor",class:{active:s==t.navIndex},on:{click:function(e){return t.navChangeClick(s)}}},[i("img",{attrs:{src:"https://ec.wexiang.vip/source/static/img/user_0"+(s+1)+(s==t.navIndex?"_act":"")+".png",alt:""}}),i("span",[t._v(t._s(e))])])}),0)])],1),i("div",{staticClass:"mark-main flex-1"},[i("div",{staticClass:"tools flex align-center"},[i("div",{staticClass:"backspace u-f-justify cursor mr10",on:{click:function(e){return t.$router.go(-1)}}},[i("div",{staticStyle:{width:"24px",height:"20px"}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/3.png"}})])]),i("div",{staticClass:"backspace u-f-justify cursor mr10",on:{click:t.cutAlbumClick}},[t.album?i("div",{staticStyle:{width:"23px",height:"20px"}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/9.png"}})]):i("div",{staticStyle:{width:"20px",height:"20px"}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/7.png"}})])]),i("div",{staticClass:"big-small flex u-f-item"},[i("div",{staticClass:"big",class:{active:t.amplification},on:{click:function(e){t.amplification=!0}}},[i("div",{},[t.amplification?i("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/13.png",alt:""}}):i("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/14.png",alt:""}})])]),i("div",{staticClass:"small ",class:{active:!t.amplification},on:{click:function(e){t.amplification=!1}}},[i("div",[t.amplification?i("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/15.png",alt:""}}):i("img",{attrs:{src:"https://ec.wexiang.vip/source/img2/16.png",alt:""}})])])]),i("el-select",{attrs:{placeholder:"请选择","popper-class":"select","popper-append-to-body":!1},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}},t._l(t.options,function(t){return i("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})}),1),i("el-select",{staticStyle:{"margin-left":"20px"},attrs:{placeholder:"请选择","popper-class":"select","popper-append-to-body":!1},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}},t._l(t.options,function(t){return i("el-option",{key:t.value,attrs:{label:t.label,value:t.value}})}),1),i("div",{staticClass:"search-card flex-1"},[i("el-input",{attrs:{"prefix-icon":"el-icon-search",placeholder:"搜索"},on:{change:t.inputChange},model:{value:t.keyword,callback:function(e){t.keyword=e},expression:"keyword"}})],1),t.selfFlag?t._e():i("div",{staticClass:"backspace u-f-justify cursor ml10",on:{click:t.setClick}},[i("div",{staticStyle:{width:"20px",height:"22px"}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/4.png"}})])])],1),i("div",{ref:"scroll",staticClass:"article ",class:{articleSoll:t.isfixed},on:{scroll:t.scroll}},[i("transition",{attrs:{name:"el-fade-in-linear"}},[t.album?i("div",{staticClass:"list u-f u-f-jsb",on:{scroll:t.scroll}},[t._l(t.selectAlbumList,function(e,s){return i("div",{key:s,staticClass:"item",class:{item1:t.amplification},on:{click:function(i){return t.albumItemClick(e)}}},[i("div",{staticClass:"itemImg",class:{itemImg1:t.amplification}},[i("el-image",{staticClass:"img",attrs:{src:e.cover,fit:"cover"}})],1),i("p",{staticClass:"itemName singleLine"},[t._v(t._s(e.type_name))]),i("p",{staticClass:"itemDescribe singleLine"},[t._v(t._s(e.desc))]),i("div",{staticClass:"information"},[i("div",{staticClass:"informationTop u-f"},[i("div",{staticClass:"informationTopLtfe"},[t._v("地板价")]),i("div",{staticClass:"informationTopRight"},[t._v("收录")])]),i("div",{staticClass:"informationBottom u-f"},[i("div",{staticClass:"informationTopLtfe singleLine"},[t._v(t._s(e.min_price))]),i("div",{staticClass:"informationTopRight singleLine"},[t._v(t._s(e.included_total)+"\n\t\t\t\t\t\t\t\t\t\t\t")])])])])}),t._l(10,function(e){return i("i",{staticClass:"ListIcon",class:{ListIcon1:t.amplification}})})],2):i("div",{staticClass:"u-f u-f-justify",staticStyle:{"flex-wrap":"wrap"}},[t._l(t.List,function(e,s){return i("div",{key:s,staticClass:"articleItem cursor",on:{click:function(i){return t.itemClick(e)}}},[i("div",{staticClass:"articleItemImg",class:{amplification:t.amplification}},[i("el-image",{staticClass:"img",staticStyle:{"border-radius":"10px"},attrs:{src:5==e.type?e.image_url+"?x-oss-process=image/crop,x_1900,y_2000,w_1200,h_1200/format,webp":e.image_url,fit:"cover"}},[i("div",{staticClass:"image-slot ilRbjP",class:{amplification:t.amplification},attrs:{slot:"placeholder"},slot:"placeholder"})])],1),i("div",{staticClass:"particulars"},[i("p",{staticClass:"articleItemName singleLine",class:{articleItemName1:t.amplification}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(e.name)+"\n\t\t\t\t\t\t\t\t\t\t")]),i("p",{staticClass:"explain singleLine"},[t._v("#"+t._s(e.sn))]),i("div",{staticClass:"u-f-item"},[i("div",{staticStyle:{width:"20px",height:"20px"}},[i("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img/48.png",alt:""}})]),i("p",{staticClass:"articleItemPrice"},[t._v(t._s(e.price))])])])])}),t._l(10,function(e){return i("i",{staticClass:"articleItemImgIcon",class:{articleItemImgIcon1:t.amplification}})})],2)])],1)])])],1):t._e()])],1)},a=[],r=i("75fc"),c=(i("96cf"),i("3b8d")),n={data:function(){return{userInfo:{},type:[],filter:null,options:[{value:"选项1",label:"最新上市"},{value:"选项2",label:"推荐内容"},{value:"选项3",label:"热门"}],value:"",keyword:"",isfixed:!1,navData:["已收藏","已创建","寄售","拍卖","出价","成交记录","喜欢的"],navIndex:0,marketData:[],times:null,List:[],amplification:!1,album:!1,usercenter:{page:1,count:0},selectAlbumList:[],albumList:{page:1,count:0},wrapScrollTop:0,scrollTop:0}},mounted:function(){this.$refs.scrollMenuRef.wrap.addEventListener("scroll",this.scrollMenu)},activated:function(){var t=Object(c["a"])(regeneratorRuntime.mark(function t(){var e,i=this;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:this.List=[],e=0;case 2:if(!(e<this.usercenter.page)){t.next=8;break}return t.next=5,this.getusercenter(e+1);case 5:e++,t.next=2;break;case 8:this.getUser(),this.$nextTick(function(){i.$refs.scrollMenuRef.wrap.scrollTo(0,i.wrapScrollTop,0),i.$refs.scroll&&i.$refs.scroll.scrollTo(0,i.scrollTop,0)});case 10:case"end":return t.stop()}},t,this)}));function e(){return t.apply(this,arguments)}return e}(),computed:{selfFlag:function(){return this.$route.query.id==this.userInfo.id}},methods:{getUser:function(){var t=this;this.$route.query.id?this.$post({method:"user.info",user_id:this.$route.query.id}).then(function(e){t.userInfo=e.data.data}):this.$post({method:"user.info"}).then(function(e){t.userInfo=e.data.data})},getusercenter:function(t){var e=this;console.log(t),this.$post({method:"user.getusercenter",status:this.navIndex+1,page:t,limit:30,keyword:this.keyword,user_id:this.$route.query.id}).then(function(t){e.usercenter.count=t.data.data.count,e.List=[].concat(Object(r["a"])(e.List),Object(r["a"])(t.data.data.list))})},itemClick:function(t){var e="",i="";1==t.type?i="colleagues":2==t.type?i="audio":3==t.type?i="game":4==t.type?i="ikon":5==t.type&&(i="brandRole"),e=1==t.is_market?"/bazaar/particulars":"/explore/particulars",this.$router.push({path:e,query:{id:t.goods_id,type:i}})},albumItemClick:function(t){t.is_market?this.$router.push({path:"/explore/albumDetails",query:{id:t.id}}):this.$router.push({path:"/bazaar/albumDetails",query:{id:t.id}})},scrollMenu:function(){var t=this;this.times&&clearTimeout(this.times),this.times=setTimeout(function(){var e=t.$refs.scrollMenuRef.wrap.scrollTop;t.wrapScrollTop=e;t.$refs.scrollMenuRef.wrap.scrollHeight;t.filter="blur(".concat(e/40,"px)"),t.isfixed=e>491},10)},navChangeClick:function(t){this.navIndex=t,this.usercenter.page=1,this.List=[],this.getusercenter(this.usercenter.page)},setClick:function(){this.$router.push({path:"/setting"})},scroll:function(t){var e=t.target.scrollTop,i=t.target.clientHeight,s=t.target.scrollHeight;this.scrollTop=e,e+i===s&&(this.List.length<this.usercenter.count&&!this.album?(this.usercenter.page++,this.getusercenter(this.usercenter.page)):this.selectAlbumList.length<this.albumList.count&&!this.album&&(this.albumList.page++,this.getSelectAlbum()))},inputChange:function(){this.page=1,this.List=[],this.getusercenter(this.usercenter.page)},cutAlbumClick:function(){this.album=!this.album,this.selectAlbumList.length||this.getSelectAlbum()},getSelectAlbum:function(){var t=this;this.$get({method:"articles.getArticleType",page:this.albumList.page,limit:30,type:-1,is_draft:0,is_my:1,other_user_id:this.userInfo.id,status:-1}).then(function(e){e.data.status&&(t.albumList.count=e.data.data.count,t.selectAlbumList=[].concat(Object(r["a"])(t.selectAlbumList),Object(r["a"])(e.data.data.list)))})},attentionClick:function(){var t=this;localStorage.getItem("token")?this.$post({method:"user.userlove",to_user_id:this.userInfo.id,type:this.userInfo.is_love?0:1}).then(function(e){t.getUser()}):this.$store.commit("account/onRegister",!0)}}},l=n,o=(i("905d"),i("a5e4"),i("e3b8"),i("3f87"),i("2877")),u=Object(o["a"])(l,s,a,!1,null,"43ae66b5",null);e["default"]=u.exports},d2d5:function(t,e,i){i("1654"),i("549b"),t.exports=i("584a").Array.from},e3b8:function(t,e,i){"use strict";var s=i("493d"),a=i.n(s);a.a},f410:function(t,e,i){i("1af6"),t.exports=i("584a").Array.isArray}}]);