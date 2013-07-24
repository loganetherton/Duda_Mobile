    function dotplay(run){
        if(run===false){
            dots=false;
            return;
        }
        if(run==null)
            dots=true;
        if(dots){
            
            setTimeout('$("#dots").html(".")',0);
            setTimeout('$("#dots").html("..")',500);
            setTimeout('$("#dots").html("...")',1000);
            setTimeout(function(){
                dotplay(1);
            }, 1500);
        }
    }
    
    function showloading(){
        $("#mobilizebtn").attr('class','inactive');
        $("#ribbon").hide();
        $("#frog").show();
        $("#text4").show();
        dotplay();
    }
    
    function editsite(){
        
        $.post('dudamobile.live.php','ajax=true&action=editsite', function(data) {
            var ret=mgdecode(data);
            if(ret['result']=='success'){
                
                $("#title1").hide();
                $("#pic").hide();
                $("#list").hide();
                $("#mobilizebtn").hide();
                $("#text4").hide();
                $("#frog").hide();
                $("#shell").attr('src','img/nexus-1.jpg');
                $("#shell").css('margin','0px');
                $(".body-content").css('height','710px');
                $("#preview").attr('src',ret['previewlink']);
                
                $("#title2").show();
                $("#text1").css("display","inline-block");
                $("#text2").show();
                $("#text3").show();
                $("#linkframe").show();
                $("#preview").show();
                
                
            }
            else{
                $("#error").html('Unknown Error');
            }
            dotplay(false);
        });
    }
    
    function mgdecode(str){
                    var items = str.split('||');
                    var ret = {}, temp;
                    for (var i=0, n=items.length; i < n; i++) {
                            temp = items[i].split('**');
                            ret[temp[0]]=temp[1];
                    }
                    return ret;
    }
    function make(onlysite){
        showloading();
        $.post('dudamobile.live.php','ajax=true&action=makesite&onlysite='+onlysite, function(data) {
        var ret=mgdecode(data);
        if(ret['result']=='success'){
            editsite();
        }
        else  if(ret['result']=='failure'){
                $("#error").html('Unexpected Error during '+ret['during']+': '+ret['error']);
        }
        else{
                $("#error").html('Unexpected Error: '+data);
        }
        $("#preloader").hide();
        });
    }
    function disable(){
        loading();
        $("#leftside").hide();
        $("#rightside").hide();
        $.post('dudamobile.live.php','ajax=true&action=disablesite', function(data) {
            var ret=mgdecode(data);
            if(ret['result']=='success'){
                window.location="";
            }
            else if(ret['result']=='failure'){
                    $("#error").html('Unexpected Error during '+ret['during']+': '+ret['error']);
            }
            else{
                    $("#error").html('Unexpected Error: '+data);
            }
        });
    }
    function enable(){
        showloading();
        $.post('dudamobile.live.php','ajax=true&action=enablesite', function(data) {
            var ret=mgdecode(data);
            if(ret['result']=='success'){
                editsite();
            }
            else if(ret['result']=='failure'){
                    $("#error").html('Unexpected Error during '+ret['during']+': '+ret['error']);
            }
            else{
                    $("#error").html('Unexpected Error: '+data);
            }
        });
    }
    function loading(run){

        if(run===false){
            load=false;
            $("#loading").hide();
            return;
        }
        if(run==null){
            $("#loading").show();
            load=true;
        }
        if(load){

            setTimeout('if(load) $("#loading span").html("Loading.")',0);
            setTimeout('if(load) $("#loading span").html("Loading..")',500);
            setTimeout('if(load) $("#loading span").html("Loading...")',1000);
            setTimeout(function(){
                loading(1);
            }, 1500);
        }
    }
    
    function showcontent(){
        loading(false);
        $("#leftside").show();
        $("#rightside").show();
    }

    function redirectToSiteEditor() {
        $.post('dudamobile.live.php','ajax=true&action=redirecttoeditor', function(data) {
            if (data) {
                window.location = data;
            }
        });
    }

    $(document).ready(function(){

        var dots=false;
        var load=false;
        var show=true;

        loading();
        $.post('dudamobile.live.php','ajax=true&action=checksite', function(data) {
            switch(data){
                    case 'enabled':
                            showloading();
                            editsite();
                    break;
                    case 'disabled':
                            $("#mobilizebtn").attr('class','active').attr('onclick','enable(); return false;');
                    break;
                    case 'notexists':
                            var onlysite='false';
                            show=false;
                            $.post('dudamobile.live.php','ajax=true&action=checkaccount', function(res) {
                                if(res=='exists') onlysite='true';
                                $("#mobilizebtn").attr('class','active').attr('onclick','make("'+onlysite+'"); return false;');
                                showcontent();
                            });
                            
                    break;
                    default:
                        $("#error").html('Unexpected Error: Can\'t connect to DudaMobile');
                        show=false;
                    break;
            }
            
            if(show) showcontent();
            
        });

    });
