/**
 *
 *
 *
 */
var imgId=4; //默认为第四张开始
var weatherId=0;	//最终添加的天气ID
var chooseWeather=0; //切换的天气ID
var totalCount=18;
var weatherTotal=4;
var weather=new Array(8);
var bgs=new Array(9);
var bgsCount=7;
weather[0]="晴天";
weather[1]="下雨";
weather[2]="下雪";
weather[3]="多云";
weather[4]="阴天";
bgs[0]="可爱兔兔";
bgs[1]="彩虹心情";
bgs[2]="放飞自由";
bgs[3]="粉色记忆";
bgs[4]="快乐雪人";
bgs[5]="网校三周年";
bgs[6]="每日学习进程";
bgs[7]="甜蜜爱情";

//图片切换
function ChangePic(i)
{
	//下一张
	if(i==1&&imgId!=totalCount)
	{
		imgId+=1;
		$("#priview_img").css("background","url(./images/emoticons/"+imgId+".gif) no-repeat center");
		$("#pic_priview_msg").hide(50);
	}
	
	if(imgId==totalCount)
	{
		imgId=0;
		ChangePic(1);
	}
}

//天气切换
function ChangeWeather()
{
	if(chooseWeather==weatherTotal)
		chooseWeather=0;
	else
		chooseWeather+=1;
	$("#pnl_weather").css("background","url(./images/weather/"+chooseWeather+".gif) no-repeat");
	$("#pnl_weather").attr("title",weather[chooseWeather]);
}

$(function(){
	//默认标题与表格5的时间
	var dt = new Date();
	var tb_title5 = dt.getFullYear()+'-'+(dt.getMonth()+1)+'-'+dt.getDate();
	$("#lb_Date").html(tb_title5);
	$("#txtTitle,#hide_today").val(tb_title5);

	var isAuto=true;

	//自动切换天气
	var MyInterval=setInterval("ChangeWeather()",900);
	
	$("#priview_img").click(function(){
		ChangePic(1); //下一张
	});
	
	$("#pnl_weather").click(function(){
		if(isAuto)
		{
			clearInterval(MyInterval);
			isAuto=false;
		}
		else
		{
			ChangeWeather();
		}
		$("#pic_weather_msg").hide(100);
		weatherId=chooseWeather;
	})
	
	$(".lb_title").attr("title","点击可修改哦");
	
	$(".lb_title").click(function(){
		if($(this).find(".txt_lb_title").val()==null && $(this).attr("id")!='lb_Date')
		{
			$(this).html("<input type='text' class='txt_lb_title' value='"+$(this).html()+"'/>");
			$(this).find(".txt_lb_title").focus();
			$('.txt_lb_title').inputlimiter({
				limit: 9,
				remText: '<div class=limt3>字</div> <div class=limt2>%n</div> <div class=limt1>你还可以输入</div>',
				limitText: ''
			});
			$(this).find(".txt_lb_title").blur(function(){
				$(this).parent().html($(this).val());
			});
		}
	});
	
	$("#table_9").find("textarea").inputlimiter({
		limit: 32,
		remText: '<div class=limt3>字</div> <div class=limt2>%n</div> <div class=limt1>你还可以输入</div>',
		limitText: ''
	});
	
	$("#txtTitle").inputlimiter({
		limit: 25,
		remText: '<div class=limt3>字</div> <div class=limt2>%n</div> <div class=limt1>你还可以输入</div>',
		limitText: ''
	});
	
	$("#txtTitle").focus();
	
	//上一天
	$("#link_preDate").click(function(){
		
		var date_today=$("#hide_today").val(); //获取今天日期
		
		var date_now=$("#lb_Date").html(); //获取当前日期
		
		var date_between=daysBetween(date_now,date_today);
		
		if(date_between<3) //3天以内
		{
			date_now=beforeday(-1);
			$("#lb_Date").html(date_now);
		}
	});
	
	//下一天
	$("#link_nextDate").click(function(){
		
		var date_today=$("#hide_today").val(); //获取今天日期

		var date_now=$("#lb_Date").html(); //获取当前日期

		var date_between=daysBetween(date_now,date_today);
		
		if(date_between!=0) //3天以内
		{
			date_now=beforeday(1);
			$("#lb_Date").html(date_now);
		}
	});
});

//切换
function dochange(temp_str)
{
	for(var i=1;i<9;i++)
	{
		$("#lb_title_"+i).html(temp_str[i]);
	}
}

function doProduce(userId)
{	
	var title="";
	var tempId=1;
	var mid_title="";
	var bgid=1;
	//获取日记名称
	title=$("#txtTitle").val();
	
	//获取背景ID
	bgid=$("#hide_bgId").val();

	//获取日记中间格标题
	mid_title=$("#lb_Date").html();
	
	//获取模板Id
	tempId=$("#hide_tempId").val();
	
	//是否是私密日记
	var isPersonal = 1;
	if($('#chkIsPersonal').attr("checked")) isPersonal=0;
	
	
	if(title.length==0)
	{
		return ShowError("日记标题要填哦~");
	}
	var cnt="";
	var cnt_titles="";
	var isnull = 1;
	//获取每一块的信息
	for(var i=1;i<9;i++)
	{
		if($("#txt_diary_"+i).val().length==0)
		{
			isnull ++;
		}
		if(i==8)
		{
			cnt+=$("#txt_diary_"+i).val();
			cnt_titles+=$("#lb_title_"+i).html();
		}
		else
		{
			cnt+=$("#txt_diary_"+i).val()+"-|*dt*|-";
			cnt_titles+=$("#lb_title_"+i).html()+"-|*dt*|-";
		}
	}
	//alert(isnull);
	if (isnull == 9){
		return ShowError("日记内容都要填哦~");
	}
	/*var isToIng=false;
	
	isToIng=$("#chkIsToIng").attr("checked");*/
	
	$("#pnl_produce").html("<div id='pnl_waiting' style='color:gray'>正在生成九宫格，请稍等...</div>");
	$.post('./do.php',{bgid:bgid,title:title,cnt:cnt,imgId:imgId,weatherId:weatherId,mid_title:mid_title,cnt_titles:cnt_titles,tempId:tempId,open:isPersonal},function(response){
		if (response.status) {
			//return ShowError("<img src=\"./upfile/"+response.info+"\" />");
			window.open("./upfile/"+response.info);
	        $("#btn_produce").click(function () {
	            doProduce();
	        });
	    }
	    else {
			return ShowError(response.info);
	        
	    }
		$("#pnl_produce").html("<span>系统也要休息</span>");
		//设置延时才能再次进行添加日志
		setTimeout(function(){
			$("#pnl_produce").html("<a onclick='doProduce()' href='###' id='btn_produce'></a>");
		},60000);
	},'json');

}

//显示背景更换
function showChangeBg()
{
	$("#winmedal-content").removeClass(); 
	$("#winmedal-content").addClass("ChangeBg-content");
	var bgstr="<ul>";
	for(var i=0;i<=bgsCount;i++)
	{
		bgstr+="<li class='bg_priview_li f_left'><img height='120' width='120' onclick='changeBg("+i+")' class='bg_priview' src='./images/preview/"+i+".gif'>"+bgs[i]+"</li>";
	}
	bgstr+="</ul>";
	
	$("#winmedal-content").html("<h2 style='margin-bottom: 10px;' class='big green'>更换九宫格背景</h2>"+bgstr);
	$("#winmedal-box").show();
}

//切换背景
function changeBg(bid)
{
	$(".diary").css("background","url('./images/preview/"+bid+".jpg') no-repeat");
	$("#hide_bgId").val(bid);
	closeAddCate();
}

//显示警告窗
function ShowError(content)
{
	$("#winmedal-content").removeClass(); 
	$("#winmedal-content").addClass("Error-content");
	$("#winmedal-content").html("<img style='float:left; margin:4px 5px 0px 0px;' src='./images/icon_error.gif' /> <span class='big' style='color:gray;'>"+content+"</span>");
	$("#winmedal-box").show(300);
	return false;
}

//关闭添加分类窗口
function closeAddCate()
{
	$('#winmedal-box').hide(100);
	return false;
}

//求两个时间的天数差 日期格式为 YYYY-MM-dd
function daysBetween(DateOne,DateTwo)
{
	var OneMonth = DateOne.substring(5,DateOne.lastIndexOf ('-'));
	var OneDay = DateOne.substring(DateOne.length,DateOne.lastIndexOf ('-')+1);
	var OneYear = DateOne.substring(0,DateOne.indexOf ('-'));

	var TwoMonth = DateTwo.substring(5,DateTwo.lastIndexOf ('-'));
	var TwoDay = DateTwo.substring(DateTwo.length,DateTwo.lastIndexOf ('-')+1);
	var TwoYear = DateTwo.substring(0,DateTwo.indexOf ('-'));

	var cha=((Date.parse(OneMonth+'/'+OneDay+'/'+OneYear)- Date.parse(TwoMonth+'/'+TwoDay+'/'+TwoYear))/86400000);
	return Math.abs(cha);
}


//这个方法用来进行日期的前一天和后一天的移动
function beforeday(obj)
{
	/**
	 *获取日期文本框的值将获取到得值赋值给变量
	**/
	var starthidden=$("#lb_Date").html();
	/**
	 *正则匹配日期
	**/
	var reg = /^(\d{4})-(\d{1,2})-(\d{1,2})/;
	/**
	 *如果传入的值为1 将日期加上一天
	 *如果传入的值为-1 将日期加减去一天
	**/
	if(obj==1)
	{
		if(reg.test(starthidden))
		{
			//调用进行日期的加减的方法dateAdd
			var d = dateAdd(RegExp.$1,RegExp.$2,RegExp.$3,1);
			starthidden=d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
			$("#lb_Date").html(starthidden);
		}
	}
	else
	{
		if(reg.test(starthidden))
		{
			//调用进行日期的加减的方法dateAdd
			var d = dateAdd(RegExp.$1,RegExp.$2,RegExp.$3,-1);
			starthidden=d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
			$("#lb_Date").html(starthidden);
		}
	}
}
//这个方法用来进行日期的加减
function dateAdd(y,m,d,n)
{
   var d = new Date(y,m-1,d);
   d.setDate(d.getDate()+n);
   return d;
}






