(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-02fd7e2a"],{"14fc":function(t,s,e){},1546:function(t,s,e){"use strict";var i=e("e32a"),a=e.n(i);a.a},"1cfe":function(t,s,e){},"2bbc":function(t,s,e){},"2d52":function(t,s,e){"use strict";var i=e("bd39"),a=e.n(i);a.a},"3df2":function(t,s,e){},4027:function(t,s,e){"use strict";var i=e("2bbc"),a=e.n(i);a.a},"54a5":function(t,s,e){"use strict";var i=e("3df2"),a=e.n(i);a.a},"5d81":function(t,s,e){t.exports=e.p+"src/media/1.347e0dc0.webm"},"6fc8":function(t,s,e){t.exports=e.p+"src/media/2.582615ae.webm"},"947b":function(t,s,e){t.exports=e.p+"src/media/3.6c87124a.webm"},b4e2:function(t,s,e){"use strict";var i=e("14fc"),a=e.n(i);a.a},b944:function(t,s,e){"use strict";e.r(s);var i=function(){var t=this,s=t.$createElement,e=t._self._c||s;return t.info.cover?e("div",{staticClass:"content"},[e("summon",{directives:[{name:"show",rawName:"v-show",value:1==t.state,expression:"state==1"}],attrs:{info:t.info},on:{summonClick:t.summonClick,summonAnimationEnded:t.summonAnimationEnded}}),e("div",{directives:[{name:"show",rawName:"v-show",value:3==t.state,expression:"state==3"}],staticClass:"summon"},[t.content?e("div",[t.pleasantly?e("div",{staticClass:"bjcImg pleasantly"},[e("video",{staticClass:"head_video",attrs:{src:"https://ec.wexiang.vip/source/img1/special1.webm",autoplay:"autoplay",muted:"muted"},domProps:{muted:!0},on:{ended:function(s){return t.onPlayerEnded(s)}}})]):t._e()]):t._e(),e("transition",{attrs:{name:"fade1"}},[e("div",{directives:[{name:"show",rawName:"v-show",value:t.roleInfoName,expression:"roleInfoName"}],staticClass:"information u-f-column u-f-item"},[e("div",{staticClass:"nameBjc u-f-justify u-f-column "},[e("p",{staticClass:"name"},[t._v(t._s(t.roleInfo.name))]),e("p",{staticClass:"nameEng"},[t._v(t._s(t.roleInfo.name_eng))])])])])],1),e("transition",{attrs:{"enter-active-class":"animate__animated animate__fadeIn","leave-active-class":"animate__animated animate__fadeOut "}},[e("div",{directives:[{name:"show",rawName:"v-show",value:t.title,expression:"title"}],staticClass:"title"},[t._v("\n\t\t\t"+t._s(t.stateText)+"\n\t\t\t"),e("div",{staticClass:"wire"})])]),e("transition",{attrs:{"enter-active-class":"animate__animated "+t.animate__fadeInUpBig,"leave-active-class":"animate__animated animate__fadeOutUpBig "}},[e("information",{directives:[{name:"show",rawName:"v-show",value:4==t.state,expression:"state==4"}],attrs:{roleInfo:t.roleInfo},on:{advance:t.advance}})],1),e("transition",{attrs:{"enter-active-class":"animate__animated animate__fadeInUpBig","leave-active-class":"animate__animated animate__fadeOutDownBig "}},[e("preview",{directives:[{name:"show",rawName:"v-show",value:5==t.state,expression:"state==5"}],attrs:{roleInfo:t.roleInfo},on:{again:function(s){t.state=1},rollback:t.rollback,expressionItem:t.expressionItem,finishClick:t.finishClick}})],1),1!=t.state&&2!=t.state?e("div",{staticClass:"bjcImg"},[e("video",{ref:"media",staticClass:"head_video",attrs:{src:"https://ec.wexiang.vip/source/img1/special2.webm",loop:"loop",muted:"muted"},domProps:{muted:!0}})]):t._e(),2==t.state||3==t.state||4==t.state?e("div",{staticClass:"arrows cursor",on:{click:t.advance}},[e("img",{attrs:{src:"https://ec.wexiang.vip/source/img1/44.png",alt:""}})]):t._e(),1!=t.state&&2!=t.state?e("div",{staticClass:"shadow"}):t._e(),e("transition",{attrs:{name:t.fade}},[e("el-image",{directives:[{name:"show",rawName:"v-show",value:t.show&&1!=t.state&&2!=t.state,expression:"show&&state!=1&&state!=2"}],staticClass:"img prize",attrs:{src:t.roleInfo.role_img,fit:"scale-down"},on:{load:t.load}},[e("div",{attrs:{slot:"placeholder"},slot:"placeholder"}),e("div",{attrs:{slot:"error"},slot:"error"})])],1)],1):t._e()},a=[],n=function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{staticClass:"summon"},[i("transition",{attrs:{name:"el-fade-in-linear"}},[i("div",{staticClass:"summonImg",style:{zIndex:t.show?1:9}},[i("video",{staticClass:"head_video img",attrs:{src:e("5d81"),autoplay:"autoplay",muted:"muted"},domProps:{muted:!0},on:{ended:function(s){return t.onPlayerEnded(s)}}})])]),i("transition",{attrs:{name:"el-fade-in"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:t.show,expression:"show"}],staticClass:"summonImg",style:{zIndex:t.show?9:1}},[i("video",{ref:"media",staticClass:"head_video img",attrs:{src:e("6fc8"),loop:"loop",autoplay:"autoplay",muted:"muted"},domProps:{muted:!0}})])]),i("transition",{attrs:{name:"el-fade-in"}},[t.summonAnimation?i("div",{staticClass:"summonImg",staticStyle:{"z-index":"11"}},[i("video",{staticClass:"head_video img",attrs:{src:e("947b"),autoplay:"autoplay",muted:"muted"},domProps:{muted:!0},on:{ended:function(s){return t.summonAnimationEnded(s)}}})]):t._e()]),i("transition",{attrs:{name:"el-fade-in"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:t.show,expression:"show"}],staticClass:"operation",style:{zIndex:t.show?10:1}},[i("div",{staticClass:"button cursor",on:{click:t.summonClick}},[i("el-image",{staticClass:"img ",attrs:{src:t.gold1,fit:"cover"}})],1),i("div",{staticClass:"time u-f u-f-item"},[i("div",[i("el-image",{staticClass:"img",attrs:{src:t.gold,fit:"cover"}})],1),i("p",[t._v("消耗卡券 ：1 ")]),i("p",{staticStyle:{"margin-left":"20px"}},[t._v("拥有：0")])])])]),t._m(0)],1)},o=[function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"sharing cursor"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/65.png",alt:""}})])}],c={props:{info:{type:Object}},data:function(){return{gold:"https://ec.wexiang.vip/source/img/brandDirectSelling/3.png",gold1:"https://ec.wexiang.vip/source/img1/69.png",show:!1,summonAnimation:!1}},methods:{summonClick:function(){var t=this;if(!localStorage.getItem("token"))return this.$store.commit("account/onRegister",!0);this.$store.commit("account/onDrawCard",!0),this.$post({method:"goods.drawcard"},!1).then(function(s){t.$post({method:"goods.findgoodsdetail",id:s.data.data},!1).then(function(s){t.$emit("summonClick",s.data.data)})}),this.summonAnimation=!0},onPlayerEnded:function(){this.show=!0,this.$refs.media.play()},summonAnimationEnded:function(){this.summonAnimation=!1,this.$emit("summonAnimationEnded")}}},r=c,l=(e("1546"),e("2877")),m=Object(l["a"])(r,n,o,!1,null,"ad0c747e",null),d=m.exports,u=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"summon"},[e("div",{staticClass:"summonImg"},[e("el-image",{staticClass:"img ",attrs:{src:t.url1,fit:"cover"}})],1),t._m(0)])},f=[function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"progressBar"},[e("div",{staticClass:"progressBarItem"},[e("div",{staticClass:"progressBarAll"}),e("div",{staticClass:"progressBarPerform"})])])}],v={data:function(){return{url1:"https://ec.wexiang.vip/source/img/brandDirectSelling/5.png"}},methods:{}},p=v,g=(e("2d52"),Object(l["a"])(p,u,f,!1,null,"4e9aa492",null)),h=g.exports,_=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"summon"},[t.content?e("div",[t.pleasantly?e("div",{staticClass:"bjcImg pleasantly"},[e("video",{staticClass:"head_video",attrs:{src:"https://ec.wexiang.vip/source/img1/special1.webm",autoplay:"autoplay",muted:"muted"},domProps:{muted:!0},on:{ended:function(s){return t.onPlayerEnded(s)}}})]):t._e()]):t._e(),e("transition",{attrs:{name:"fade"}},[e("el-image",{directives:[{name:"show",rawName:"v-show",value:t.show,expression:"show"}],staticClass:"img prize",attrs:{src:t.roleInfo.image_url,fit:"scale-down"},on:{load:t.load}},[e("div",{attrs:{slot:"placeholder"},slot:"placeholder"}),e("div",{attrs:{slot:"error"},slot:"error"})])],1),e("transition",{attrs:{name:"fade1","leave-active-class":"animate__animated animate__fadeInDownBig"}},[e("div",{directives:[{name:"show",rawName:"v-show",value:t.show,expression:"show"}],staticClass:"information u-f-column u-f-item"},[e("div",{staticClass:"nameBjc u-f-justify u-f-column "},[e("p",{staticClass:"name"},[t._v(t._s(t.roleInfo.name))]),e("p",{staticClass:"nameEng"},[t._v(t._s(t.roleInfo.name_eng))])])])])],1)},C=[],w={props:{roleInfo:{type:Object}},data:function(){return{show:!1,pleasantly:!0,content:!1}},created:function(){},methods:{advance:function(){this.show=!this.show},onPlayerEnded:function(){this.pleasantly=!1,this.$emit("onPlayerEnded"),this.show=!0},load:function(){this.content=!0}}},x=w,I=(e("ba1c"),Object(l["a"])(x,_,C,!1,null,"76343796",null)),b=I.exports,y=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"information"},[e("div",{staticClass:"content"},[e("div",{staticClass:"top u-f u-f-jsb"},[e("div",{staticClass:"figure"},[e("div",{staticClass:"u-f-item"},[e("p",{staticClass:"figureName"},[t._v(t._s(t.roleInfo.name))]),t._m(0)]),e("p",{staticClass:"figureNameEng"},[t._v("Rimuru Tempest")])]),e("div",{staticClass:"topRight"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/51.png",alt:""}}),e("div",{staticClass:"topRightContent"},[e("p",{staticClass:"topRightContentName1"},[t._v(t._s(t.roleInfo.work))]),e("p",{staticClass:"topRightContentName2"},[t._v(t._s(t.roleInfo.extend.work_life))])])])]),e("div",{staticClass:"cength u-f u-f-jsb"},[e("div",{staticClass:"character u-f-column u-f-item u-f-justify"},[t._m(1),t._l(t.roleInfo.extend.character_info,function(s,i){return e("div",{key:i,staticClass:"characterList u-f"},[e("div",{class:{itemRight:i%2!=0,itemLeft:i%2!=1},style:{visibility:i%2!=0?"hidden":"",border:i%2!=0?"none":""}},[t._v(t._s(s.name))]),e("div",{class:{itemRight:i%2!=0,itemLeft:i%2!=1},style:{visibility:i%2!=1?"hidden":"",border:i%2!=1?"none":""}},[t._v(t._s(s.name))])])}),t._m(2)],2),e("div",{staticClass:"talentSkill"},[e("div",{staticClass:"u-f"},[t._m(3),e("div",{staticClass:"talentList u-f"},t._l(t.roleInfo.extend.potential_info,function(s,i){return e("div",{key:i,staticClass:"talentItem"},[e("img",{staticClass:"img",attrs:{src:s.img,alt:""}}),e("p",{staticClass:"talentItemName"},[t._v(t._s(s.title))])])}),0)]),e("div",{staticClass:"u-f",staticStyle:{"margin-top":"30px"}},[t._m(4),e("div",{staticClass:"talentList u-f"},t._l(t.roleInfo.extend.talent_info,function(s,i){return e("div",{key:i,staticClass:"talentItem"},[e("img",{staticClass:"img",attrs:{src:s.img,alt:""}}),e("p",{staticClass:"talentItemName"},[t._v(t._s(s.title))])])}),0)])])]),e("div",{staticClass:"bottom u-f-jsb"},[e("div",{staticClass:"u-f"},[e("div",{staticClass:"designation"},[e("img",{staticClass:"bjcImg img",attrs:{src:"https://ec.wexiang.vip/source/img1/48.png",alt:""}}),t._m(5),e("div",{staticClass:"designationContent"},t._l(t.roleInfo.extend.role_title_info,function(s,i){return e("div",{key:i,staticClass:"designationContentImg u-f-justify",class:{designationContentItemImg:i>0}},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/50.png",alt:""}}),e("p",[t._v(t._s(s.title))])])}),0)]),e("div",{staticClass:"course"},[e("img",{staticClass:"bjcImg img",attrs:{src:"https://ec.wexiang.vip/source/img1/57.png",alt:""}}),t._m(6),e("div",{staticClass:"courseText"},[t._v("\n\t\t\t\t\t\t"+t._s(t.roleInfo.extend.history)+"\n\t\t\t\t\t")])])]),e("div",{staticClass:"probability"},[e("ul",{staticClass:"probabilityList"},t._l(t.roleInfo.part,function(s,i){return e("li",{staticClass:"u-f-jsb probabilityListItem"},[e("div",{staticClass:"u-f-item"},[e("p",{staticClass:"character"},[t._v("◆")]),e("p",{staticClass:"probabilityListItemName"},[t._v(t._s(s.name))])]),e("p",{staticClass:"probabilityListItemNub"},[t._v(t._s(s.number)+"%")])])}),0)])])])])},k=[function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticStyle:{width:"22px",height:"30px","margin-left":"9px"}},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/45.png",alt:""}})])},function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticStyle:{width:"142px",height:"143px"}},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/46.png",alt:""}})])},function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"characterImg"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/47.png",alt:""}}),e("p",{staticClass:"characterText"},[t._v("特性")])])},function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"talent"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/52.png",alt:""}}),e("p",[t._v("天赋")])])},function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"talent"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/56.png",alt:""}}),e("p",[t._v("潜能")])])},function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"u-f po u-f-item"},[e("div",{staticStyle:{width:"17px",height:"19px"}},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/49.png",alt:""}})]),e("div",{staticClass:"designationNmae"},[t._v("称号")])])},function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"u-f po u-f-item"},[e("div",{staticStyle:{width:"17px",height:"19px"}},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/49.png",alt:""}})]),e("div",{staticClass:"designationNmae"},[t._v("历程")])])}],E={props:{roleInfo:{type:Object}},data:function(){return{}},methods:{advance:function(){this.$emit("advance")}}},$=E,j=(e("54a5"),Object(l["a"])($,y,k,!1,null,"4a656c8c",null)),N=j.exports,T=function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"preview "},[e("div",{staticClass:"content u-f-jsb"},[t._m(0),e("div",{staticClass:"contentRigth"},[e("div",{staticClass:"bjc"}),t._m(1),e("div",{staticClass:"list"},t._l(t.roleInfo.expression,function(s,i){return e("div",{key:i,staticClass:"item",on:{click:function(e){return t.itemImgClick(s)}}},[e("div",{staticClass:"itemImg"},[e("img",{staticClass:"img",attrs:{src:s.prop_img,alt:""}})])])}),0)])]),e("div",{staticClass:"operation u-f cursor"},[e("div",{staticStyle:{width:"287px",height:"121px"},on:{click:t.rollback}},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/64.png",alt:""}})]),e("div",{staticStyle:{width:"287px",height:"121px"},on:{click:t.finish}},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/63.png",alt:""}})])])])},B=[function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"contentLeft"},[e("div",{staticClass:"bjc"}),e("div",{staticClass:"contentLeftTitle"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/58.png",alt:""}})]),e("div",{staticClass:"list u-f u-f-wrap u-f-jsb"},[e("div",{staticClass:"item"},[e("div",{staticClass:"itemImg"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/59.png",alt:""}})]),e("p",[t._v("x1")])]),e("div",{staticClass:"item"},[e("div",{staticClass:"itemImg"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/60.png",alt:""}})]),e("p",[t._v("x1")])])])])},function(){var t=this,s=t.$createElement,e=t._self._c||s;return e("div",{staticClass:"contentRigthTitle"},[e("img",{staticClass:"img",attrs:{src:"https://ec.wexiang.vip/source/img1/61.png",alt:""}})])}],P={components:{},props:{roleInfo:{type:Object}},data:function(){return{}},methods:{rollback:function(){this.$emit("rollback")},finish:function(){this.$emit("finishClick")},itemImgClick:function(t){this.$emit("expressionItem",t.role_img)}}},O=P,S=(e("fb1e"),Object(l["a"])(O,T,B,!1,null,"3601ae18",null)),A=S.exports,L={components:{summon:d,animation:h,acquisition:b,information:N,preview:A},data:function(){return{state:1,shrink:!1,timer:null,info:{},roleInfo:{},show:!1,pleasantly:!0,content:!1,animate__fadeInUpBig:"animate__fadeInUpBig",title:!1,stateText:"角色情报",fade:"fade",roleInfoName:!1,valid:!0}},created:function(){this.getdrawcardset()},methods:{summonClick:function(t){this.roleInfo=t},summonAnimationEnded:function(){this.state=3},getdrawcardset:function(){var t=this;this.$post({method:"goods.getdrawcardset"}).then(function(s){t.info=s.data.data.config})},advance:function(){var t=this;return!!this.valid&&(3==this.state?(this.roleInfoName=!1,this.$refs.media.play(),setTimeout(function(){t.state++,t.title=!0},1e3)):4==this.state?(this.title=!1,setTimeout(function(){t.state=""},600),setTimeout(function(){t.state=5,t.stateText="道具一览",t.title=!0},1200)):(clearInterval(this.timer),this.state++,this.valid=!1,void setTimeout(function(){t.valid=!0},2e3)))},rollback:function(){var t=this;return this.title=!1,this.state="",this.animate__fadeInUpBig="animate__fadeInDownBig",setTimeout(function(){t.title=!0,t.stateText="角色情报"},1200),setTimeout(function(){t.state=4},600)},expressionItem:function(t){this.roleInfo.role_img=t},onPlayerEnded:function(){this.pleasantly=!1,this.show=!0,this.roleInfoName=!0,this.$refs.media.play()},load:function(){this.content=!0},finishClick:function(){var t=this;this.fade="",setTimeout(function(){t.fade="fade"},100),this.title=!1,this.state=1,this.show=!1,this.pleasantly=!0,this.$store.commit("account/onDrawCard",!1)}}},R=L,D=(e("4027"),e("b4e2"),Object(l["a"])(R,i,a,!1,null,"6d065e59",null));s["default"]=D.exports},ba1c:function(t,s,e){"use strict";var i=e("1cfe"),a=e.n(i);a.a},bd39:function(t,s,e){},e32a:function(t,s,e){},e8f9:function(t,s,e){},fb1e:function(t,s,e){"use strict";var i=e("e8f9"),a=e.n(i);a.a}}]);