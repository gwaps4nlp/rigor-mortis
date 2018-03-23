var $blockGame;
var special = false;
var inGame = false;
var annotation_id = 0;
var sentence_id = 0;
var word_position = 0;
var progression = 0;
var turn = 0;
var mode,relation_id,object_id;
var pending_request=false;

// var upl_colors = ['green','aqua','aquamarine','chartreuse','darkgreen','darkseagreen','blue'];
// var upl_colors = [
// '#2C479E', //blue
// '#CC6E91', //pink
// '#CC0606',//red
// '#B0CC6E',
// 'darkgreen','green','seagreen','mediumseagreen','limegreen','lime','darkseagreen','lawngreen','greenyellow'];

// blue gamme
var upl_colors = [
    'RoyalBlue',
    'MediumSlateBlue',
    'CornflowerBlue',
    'DodgerBlue',
    'MediumBlue',
    'SteelBlue',
    'DarkCyan',
    'DarkBlue'
];
correct_upl_color = "rgb(0, 156, 14)";
almost_correct_upl_color = "Chocolate";
incorrect_upl_color = "#c9302c";//red
// incorrect_upl_color = "#CC0606";//red

var upl_colors_used = {};
for(var i=0;i<upl_colors.length;i++){
    upl_colors_used[upl_colors[i]] = false;
}

function getColorNotUsed(){
    var color_found = false;
    for(var i=0;i<upl_colors.length;i++){
        if(!upl_colors_used[upl_colors[i]]){
            upl_colors_used[upl_colors[i]] = true;
            color_found = true;
            return upl_colors[i];
        }
    }
    if(!color_found){
        return upl_colors[Math.floor(Math.random()*upl_colors.length)];
    }
}
function resetColorsUsed(){
    for(var i=0;i<upl_colors.length;i++){
        upl_colors_used[upl_colors[i]] = false;
    }
}

$(document).ajaxError(function(jqXHR, textStatus, errorThrown) {
    pending_request = false;
    if(textStatus.status==403)
        modal(textStatus.responseJSON.error);
    if(textStatus.status==401){
        alert(textStatus.responseJSON.error);
        window.location.href = base_url + 'auth/login';         
    }
}); 

function addWordToUpl(word_position, word){

    if($('#new-upl > .word-upl[data-word-position='+word_position+']').length>0)
        return;

    if($('#new-upl > .word-upl').length==0){
        $('#new-upl').html('&nbsp;');
        $('#new-upl').append('<div class="word-upl" data-word-position="'+word_position+'">'+word+'<i class="fa fa-trash" aria-hidden="true"></i></div>');
    } else {
        var next_found = false;
        $('#new-upl > .word-upl').each(function(){           
            if(parseInt($(this).attr('data-word-position'),10)>parseInt(word_position)){
                $('<div class="word-upl" data-word-position="'+word_position+'">'+word+'<i class="fa fa-trash" aria-hidden="true"></i></div>').insertBefore($(this));
                next_found = true;
                return false;
            }
        });
        if(!next_found){
            $('#new-upl').append('<div class="word-upl" data-word-position="'+word_position+'">'+word+'<i class="fa fa-trash" aria-hidden="true"></i></div>');            
        }
    }
    $('.upl-word[data-word-position='+word_position+']').addClass('selected');

}

$(document).ready(function(){

    $blockGame = $('#block-game');

    $( window ).resize(function() {
        resizeProgressBar();  
    });

    $(document).on('click', '.link-level', function(e){

        if($('modalEndGame').hasClass('in')){
            $('modalEndGame').modal('hide');
        }
        e.preventDefault();
        if(pending_request)
            return false;
        console.log ('[jeu.js] CLICK->.btn, id_phenomene=' + $(this).attr('id_phenomene') + ' action=' + $(this).attr('action'));
        if ($(this).hasClass('buy')||$(this).hasClass('mwe')||$(this).hasClass('disabled-mwe')){
            return false;
        }
        if ($(this).hasClass('change')||$(this).hasClass('link')){
            window.location.href=$(this).attr('href');
        }
        if ($(this).hasClass('close-modal')){
            $(this).parents('.modal').each(function() {
            $(this).modal("hide");
          });
        }

        guest = false;
        
        
        if($(this).attr('action')==undefined)
            return false;
        
        mode = $(this).attr('action');    
        inGame = true;
        relation_id = $(this).attr('id_phenomene');
        turn=0;

        ajaxLoadContent();
    });

    var index_upl=0;

    $blockGame.on('click', '.upl-word', function(){
        var word = $(this).html();
        var word_position = parseInt($(this).attr('data-word-position'),10);

        if($(this).hasClass('selected')){
            if($('#new-upl > .word-upl[data-word-position='+word_position+']').length>0){
                $(this).removeClass('selected');
                $('#new-upl > .word-upl[data-word-position='+word_position+']').remove();
                updateUplFound();
                return true;
            }
        }
       
        addWordToUpl(word_position, word);

        if($(this).prev().html()=='-')
            addWordToUpl(word_position-1, '-');
        if($(this).next().html()=='-'){
            addWordToUpl(word_position+1, '-');
        }
        updateUplFound();
    });
    var is_mousedown = false;
    $('body').on('mousedown', function(){
        is_mousedown = true;
    });
    $blockGame.on('mousemove', '.upl-word', function(){
        if(is_mousedown){
            var word = $(this).html();
            var word_position = parseInt($(this).attr('data-word-position'),10);
            addWordToUpl(word_position, word);
            updateUplFound();
        }
    });

    $('body').on('mouseup', function(){
        is_mousedown = false;
    });

    $('body').keydown(function(e){
        if(mode=='upl'){
            if(e.which == 13){
                // user is pressing return
                e.preventDefault();
                $("#btn-validate-upl").trigger("click");
            }
            if(e.which == 32){
                // user is pressing space
                e.preventDefault();
                $("#add-upl").trigger("click");
                return false;
            }
        }
    });

    $blockGame.on('click', '#add-upl', function(){
        if($('#new-upl > .word-upl').length<2) return false;
        
        var color = getColorNotUsed();
        $('#sentence > .selected').css({'color':color});
        
        $('.upl-word').removeClass('selected');

        var children = $('#new-upl').children('.word-upl');
        var words_positions = [];
        $(children).each(function(index, word){
            words_positions[index] = $(word).attr('data-word-position');
        });
        
        var upl_str = words_positions.join('-');
        
        if($('.container-validated-upl[data-upl-index="'+upl_str+'"]').length>0) {
            alert('Tu as déjà sélectionné cette expression !');
            return false;
        }
        $('#add-upl').hide();
        var upl = $('#new-upl').html();
        $('#new-upl').html('&nbsp;');
        $('#validated-upl').prepend('<div class="container-validated-upl" data-upl-index="'+upl_str+'"><span class="validated-upl">'+upl+'<i class="fa fa-trash" aria-hidden="true"></i></span></div>');
        updateUplFound();
    });

    $blockGame.on('click', '#new-upl > .word-upl', function(event){
        $('#word_index_'+$(this).attr('data-word-position')).removeClass('selected');
        $(this).remove();
        if($('#new-upl > .word-upl').length==0){
            $('#new-upl').html('&nbsp;');
        }
        if($('#new-upl > .word-upl').length<2){
            $('#add-upl').hide();
        }
        updateUplFound();
    });
    $blockGame.on('click', 'div.container-validated-upl', function(event){
        $(this).find('.word-upl').each(function(){
            var current_color = $('#word_index_'+$(this).attr('data-word-position')).css('color');
            upl_colors_used[current_color] = false;
            $('#word_index_'+$(this).attr('data-word-position')).removeClass('selected');
            $('#word_index_'+$(this).attr('data-word-position')).css({'color':''});
        });
        $(this).remove();
        updateUplFound();
    });
    $blockGame.on('click', '#btn-validate-upl', function(event){
        $('#btn-validate-upl').attr("disabled","disabled");
        $('#sentence > .upl-word').removeClass('selected');
        $('#sentence > .upl-word').removeClass('upl-word');
        resetColorsUsed();
        if($('#new-upl').hasClass('validated-upl')){
            var children = $('#new-upl').children('.word-upl');
            var words_positions = [];
            $(children).each(function(index, word){
                words_positions[index] = $(word).attr('data-word-position');
            });      
            var upl = $('#new-upl').html();
            $('#new-upl').html('&nbsp;');
            $('#validated-upl').prepend('<div class="container-validated-upl" data-upl-index="'+words_positions.join('-')+'"><span class="validated-upl">'+upl+'<i class="fa fa-trash" aria-hidden="true"></i></span></div>');
        }

        var upls_user = [];
        var index_upl = 0;

        $('.validated-upl').each(function(i,container_upl ){
            var children = $(container_upl).children('.word-upl');
            var upl = [];
            $(children).each(function(index, word){
                upl[index] = $(word).attr('data-word-position');
            });

            upls_user[index_upl] = {};
            upls_user[index_upl].words_positions = upl;
            index_upl++
        });

        $('#add-upl').hide();

        if(upls_user.length==0){
            upls_user[0] = {};
            upls_user[0].words_positions = ['0'];
        }
        
        startLoader();

        $.ajax({
            method : 'POST',
            url : base_url + 'game/' + mode + '/answer',
            data : { 'upls' : upls_user, 'sentence_id' : $('#sentence').attr('data-sentence-id')},
            success : function(response){
                processJsonAnswerUpl(response, upls_user);

            }
        });
  
    });

    $(document).on('click','#report-button', function(){
        $("#form-report")[0].reset();
        $('#submitReport').attr("disabled","disabled");
        $('body').append($('#modalReport'));
        $('#modalReport').modal("show");
        
    }); 

    $(document).on('click', '.checkboxReport', function(){
        var enable_submit=false;
        $('.checkboxReport').each(function(){
            enable_submit|=$(this).is( ":checked" );
        });
        if(enable_submit) $('#submitReport').removeAttr("disabled");
        else $('#submitReport').attr("disabled","disabled");
    });


    $(document).on('focus', '#freeReportArea', function(){
        $('#free-report').prop("checked",true);
        $('#submitReport').removeAttr("disabled");
    });

    $(document).on('submit', "#form-report" ,function( event ) {
        event.preventDefault();
        $.ajax({
            method : 'POST',
            url : base_url + 'report/send',
            data : $(this).serialize()+ '&annotation_id=' + annotation_id+ '&relation_id=' + relation_id+ '&mode=' + mode+ '&user_answer=' + word_position+ '&word=' + $('.disabled-word[word_position=' + word_position + ']').text(),
            complete: function(e, xhr, settings){
                if(e.status === 422){

                 }else if(e.status === 200){
                    $('#modalReport').modal('hide');
                    $('#report-button').html('<span style="color:#298A08" class="glyphicon glyphicon-check"></span>&nbsp;'+e.responseJSON.html);
                    $('#report-button').attr('id','report-button-disabled');
                }else{

                }
            }
        });          
    });

    $blockGame.on('click', '#next-sentence', function(){
        console.log ('[jeu.js] CLICK->.#next-sentence');
        $('.parallax').animate({
            scrollTop: 0
        }, 500);
        suivant();
    });

    $blockGame.on('mouseover', '#sentence', function(){
        $('#inventory').css("visibility","hidden");
        $('#menuObject').css({
            'background-color' : 'inherit'
        });
    });
  $("*").dblclick(function(e){
    e.preventDefault();
  });

});
    function startLoader(){
        if($('#loader').length>0){
            $('#loader-container').show();
            $('#loader').show();
        }
        else
            $('#sentence').append('<div id="loader-container"><div id="loader"></div></div>');
    }
    function hideLoader(){
        $('#loader').hide();
    } 
    function stopLoader(){
        $('#loader-container').remove();
    } 

    function delay(time){
        setTimeout(function(){ suivant() }, time);
    }

    function updateUplFound(){

        if($('#new-upl > .word-upl').length<2){
            $('#add-upl').hide();
        } else {
            $('#add-upl').show();
        }

        var upl_found=0;
        if($('#new-upl > .word-upl').length>1){
            upl_found++;
            $('#new-upl').addClass('validated-upl');
        } else {
            $('#new-upl').removeClass('validated-upl');
        }
        upl_found+=$('#validated-upl > .container-validated-upl').length;
        $('#upl_found').html(upl_found);

    }
    // ====================================================================================================
    function suivant(){
        console.log ('[jeu.js] ENTER suivant');
        if(suivant){
            $('#menuObject').show();
            $('.reponse').each(function(){
                $(this).css({
                    color : '#4a1710'
                })
            })
        }
        $.ajax({
            method : 'GET',
            url : base_url + 'game/'+ mode + '/jsonContent',
            success : function(response){
                $('.loot').remove();
                if(response != ''){
                    $('#resultat').html('');
                    $('#message-object').html('');
                    processResponse(response);
                }else{
                    $('#next-sentence').attr('disabled', 'disabled');
                }
            }
        });
    }

    function initGame(_mode,_relation){
        turn=0;
        mode=_mode;
        relation_id=_relation;
        inGame=true;
        if(typeof(tour) !== 'undefined' && mode=='training'){
            tour.init();
            tour.start();
        } else if(typeof(tourA) !== 'undefined' && mode=='game'){
            tourA.init();
            tourA.start();
        }
        ajaxLoadContent();
    }

    function ajaxLoadContent(){
        
        $('body').attr('style', "cursor: url('" +base_url +'img/curseur.png'+"'), pointer; ");
        $('#coccinelle').hide();
        pending_request = true;
        $.ajax({
            method : 'GET',
            url : base_url + 'game/'+ mode + '/begin/' + relation_id,
            dataType : 'json',
            success : loadContent,
        });
    }

    function loadContent(json){
        console.log ('[jeu.js] CLICK->.phenomene SUB.1');
        $blockGame.removeClass('center-duel');
        if(mode=='duel' || mode=='demo') {
            $('.container-site').addClass('container-game').removeClass('container-site');
        }
        else if(mode=='upl') {
            $('.container-upl').addClass('container-game').removeClass('container-upl');
        }
        if($('#sentence').length==0){
            $('.parallax').animate({
                scrollTop: 0
            }, 500);            
        }
        $blockGame.html(json.html);
        console.log("1234");
        resizeProgressBar();
        $('.refuse').popover({
            trigger: 'hover'
        });

        $.ajax({
            method : 'GET',
            url : base_url + 'game/' + mode + '/jsonContent',
            dataType : 'json',
            success : function(response){
                pending_request = false;
                console.log ('[jeu.js] CLICK->.phenomene SUB.1.1');
                console.log("2345");
                    processResponse(response);
                    resizeProgressBar();
                    if (mode == 'training' && typeof tour != 'undefined') {
                            tour.start();
                    }else if (mode == 'demo' && typeof tour != 'undefined' ) {    
                            tour.start();
                    }else if(mode == 'game' && typeof tourA != 'undefined'){
                            tourA.start();
                    }

            }
        });
    }
    
    function updateProgression(turn,nb_turns){
        console.log($('#progressBar').height());
        console.log($('#progressBar').width());
        progression = Math.round( turn / nb_turns * 100 );

        $('#progress-container').css({height: 0.98*parseInt($('#progressBar').height(),10)+'px',lineHeight: $('#progressBar').height()+'px'});
        $('#progress').css({height: $('#progressBar').height()+'px',lineHeight: 0.92*parseInt($('#progressBar').height(),10)+'px'});
        $('#progress').text(progression + '%');
        $('#phaseBar').css({
            width : progression/100*$('#progressBar').width() + 'px'
        });    
    }

    // ====================================================================================================
    function processResponse(json){

        if(json.score){
            $('.score').html(json.score);
        }  
        
        updateProgression(json['turn'], json['nb_turns']);

        if(json.html){
            hideLoader();
            $("#containerModal").html(json.html);
            if($("#modalNextLevel").length>0){
                if($("#img_level").length>0)
                    $("#img_level").attr('src',url_site('/img/level/')+'level-'+json.user.level.id+'.gif');
                $("#modalNextLevel").modal("show");
            } else {
                $('#modalEndGame').modal("show");
                incCerveaux();
                incPiece();     
            }
            return true;
        }
        // en cas d'erreur
        if(json.href){
            window.location.href = json.href;
        }

        if(json.erreur != undefined && json.erreur){
            if(json.message != undefined){
                alert(json.message);
            }else{
                alert("Une erreur s'est produite");
            }
            return;
        }

        if(json.mode != 'demo'){
            $('.refuse').show();
        }
        if(mode=='upl') {
            if(json.turn==0){
                $('#btn-validate-upl').popover({
                    trigger: 'hover'
                });
            }
            else {
                $('#btn-validate-upl').popover('dispose');
            }
        }        
        if(json.mode != undefined && json.mode == 'special'){
            special = true;
        }else{
            special = false;
        }

        $('#label-phenomenon').text(json.description);

        var afficherClassement = true;
        if(json.user){
            var profil = '<div style="text-align:left;"><img src="'+base_url+'img/level/thumbs/' + json.user.level.image + '"><br />';
            
            //Progression
            profil += 'niveau '+json.user.level.id+'</div>';
            $('#profil').html(profil);
            var score_html="";
            if (json.user.level.id == 7) {
                score_html += '<img src="'+base_url+'img/cerveau_plein.png"/>'+trans('game.max-level')+'<br />';
            } else{
                var score_user = json.user.score;
                var score_next_level = json.user.next_level.required_score;
                var score_level = json.user.level.required_score;
                var progress_score = 100*(score_user-score_level)/(score_next_level-score_level);
                var score = json.user.score.formatScore() + " / " + json.user.next_level.required_score.formatScore();
                score_html += '<div class="progress" style="margin-bottom:10px;"><div style="padding-left:5px;height:20px;line-height:17px;color:#888;position:absolute;">'+score+'</div><div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211F;width:'+progress_score+'%"></div><div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:'+(100-progress_score)+'%"></div></div>';
            }
            $('.score').html(json.user.score.formatScore());

            if(json.user.money != undefined){
                $('.money').html(json.user.money.formatScore());
                score_html += '<img src="'+base_url+'img/piece.png"/><span id="argent" class="money" style="color:#4a1710;">' + json.user.money.formatScore() + '</span>';
            }

            score_html += '<br/>';
            $('#progress_score').html(score_html);
        }

        if(mode=='upl'){
            $('#upl_found').html("0");
            $('#btn-validate-upl').attr("disabled",null);          
            $('#new-upl').html('&nbsp;');
            $('#validated-upl').html('');
            $('#btn-validate-upl').show();
            $('#next-sentence').hide();
            if(json.mode_stage=='demo' && json.turn==0){
                newModalSimple('upl-instructions');
                $('#contentModal').html($('#helpRelation').html());
                $('#modalFooter').html("<div style=\"width:100%;\" data-dismiss=\"modal\" class=\"text-center\"><button class=\"btn btn-lg btn-success\">J'ai compris</button></div>");
                $('#modalSimple').modal('show');
            } else if(json.mode_stage=='training' && json.turn==0){
                newModalSimple('upl-instructions');
                $('#contentModal').html($('#helpRelation').html());
                $('#modalFooter').html("<div style=\"width:100%;\" data-dismiss=\"modal\" class=\"text-center\"><button class=\"btn btn-lg btn-success\">J'ai compris</button></div>");
                $('#modalSimple').modal('show');
            }
            var sentence_id = json.sentence.id;
            $('#sentence').html(displaySentenceUpl(json.sentence.content));
            $('#sentence').attr('data-sentence-id',sentence_id);
            if(json.correct_upls){
                $(json.correct_upls).each(function(index_upl,upl){
                    var upl_html = '';
                    if(upl.words_positions=="0"){
                        upl_html+='<div class="word-upl" data-word-position="0">Aucune expression multi-mots dans cette phrase</div>';
                    } else {
                        var words_positions = upl.words_positions.split('-');
                        $(words_positions).each(function(index,word_position){
                            var word = $('#word_index_'+word_position).html();
                            upl_html+='<div class="word-upl" data-word-position="'+word_position+'">'+word+'</div>';
                        });
                    }
                    $('#validated-upl').prepend('<div class="container-validated-upl" data-upl-index="'+index_upl+'"><span class="validated-upl">'+upl_html+'<i class="fa fa-trash" aria-hidden="true"></i></span></div>');
                });
                updateUplFound();                
            }
        }

        turn++;
    }

    function colorizeWordsSentence(words, color, sentence_id){
        sentence_id = sentence_id || null;
        var container_sentence_id = (sentence_id)?'#sentence_'+sentence_id : "#sentence";
        for(var i=0;i<words.length;i++){
            $(container_sentence_id+' #word_index_'+words[i]).css({'color':color});
        }
    }

    function displayAnswersUpls(correct_upls, upls_user, sentence_id){
        var next = true;
        sentence_id = sentence_id || null;        
        var container_id = (sentence_id)?'#container_upls_'+sentence_id+' ':'#validated-upl ';
        if(correct_upls && correct_upls.length>0)
            for (var i = 0; i < correct_upls.length; i++) correct_upls[i].found = 0;
        $(upls_user).each(function(i, upl_user){
            if(!Array.isArray(upl_user.words_positions)) upl_user.words_positions = upl_user.words_positions.split('-');
            var correct_answer = true;
            upl_user.correct = 0;
            $(correct_upls).each(function(index_upl,correct_upl){
                var correct_words_positions = correct_upl.words_positions.split('-');
                var intersect = $(correct_words_positions).filter(upl_user.words_positions);
                if(intersect.length==correct_words_positions.length && upl_user.words_positions.length==correct_words_positions.length){
                    correct_upl.found = 1;
                    upl_user.correct = 1;
                }
                else if(correct_words_positions.length>=2 && intersect.length>=correct_words_positions.length-1 && upl_user.words_positions.length >= correct_words_positions.length-1 && upl_user.words_positions.length <= correct_words_positions.length+1){
                    correct_upl.found = 1;
                    upl_user.correct = -1;
                    upl_user.correct_answer = getUplHtml(correct_upl.words_positions, sentence_id);
                }
            });
        });

        if(correct_upls && correct_upls.length>0){
            next = false;
            $(upls_user).each(function(i, upl_user){

                var upl_index = upl_user.words_positions.join('-');
                if($(container_id+'.container-validated-upl[data-upl-index="'+upl_index+'"]').length==0 && upl_index != "0"){
                    var upl_html = getUplHtml(upl_user.words_positions,sentence_id);
                    console.log(upl_html);
                    $(container_id).prepend('<div class="container-validated-upl" data-upl-index="'+upl_index+'"><span class="validated-upl">'+upl_html+'</span></div>');
                }
                if(upl_user.correct==0){
                    if(upl_index==0)
                        $(container_id+'.container-validated-upl[data-upl-index="0"]').remove();
                    else {
                        colorizeWordsSentence(upl_user.words_positions,incorrect_upl_color,sentence_id);
                        $(container_id+'.container-validated-upl[data-upl-index="'+upl_index+'"]').addClass('incorrect_upl').removeClass('container-validated-upl').append($('<span class="commentary-upl">').html("Ce n'est pas une expression multi-mots !"));
                    }
                } else if (upl_user.correct<0) {
                    colorizeWordsSentence(upl_user.words_positions,almost_correct_upl_color,sentence_id);
                    $(container_id+'.container-validated-upl[data-upl-index="'+upl_index+'"]').addClass('almost_correct_upl').removeClass('container-validated-upl').append($('<span class="commentary-upl">').html("Presque ! La réponse exacte est : "+upl_user.correct_answer));
                } else if (upl_user.correct==1) {
                    if(parseInt(upl_index,10)==0){
                        $(container_id).append('<div class="correct_upl" data-upl-index="0"><span class="validated-upl"><div class="word-upl" data-word-position="0">Aucune expression multi-mots dans cette phrase</div></span> Réponse exacte !</div>');
                    }
                    else {
                        colorizeWordsSentence(upl_user.words_positions,correct_upl_color,sentence_id);
                        $(container_id+'.container-validated-upl[data-upl-index="'+upl_index+'"]').addClass('correct_upl').removeClass('container-validated-upl').append($('<span class="commentary-upl">').html("Réponse exacte !"));
                    }
                }
            });     
            $(correct_upls).each(function(index_upl,correct_upl){
                if(correct_upl.found != 1) {
                    if(correct_upl.words_positions=="0"){
                        var upl_html='<div class="word-upl" data-word-position="0">Aucune expression multi-mots dans cette phrase</div>';
                        $(container_id).prepend('<div class="container-validated-upl" data-upl-index="0"><span class="validated-upl">'+upl_html+'</span></div>');
                        $(container_id+'.container-validated-upl[data-upl-index="0"]').addClass('correct_upl').removeClass('container-validated-upl').append($('<span class="commentary-upl">').html(" était la bonne réponse !"));
                    } else {
                        var words_positions = correct_upl.words_positions.split('-');

                        var upl_index = words_positions.join('-');
                        var upl_html = getUplHtml(words_positions,sentence_id);
                        colorizeWordsSentence(words_positions,correct_upl_color,sentence_id);
                        $(container_id).append('<div class="container-validated-upl" data-upl-index="'+upl_index+'"><span class="validated-upl">'+upl_html+'</span></div>');
                        var message = "";
                        if(!sentence_id)
                            message+="Attention ! ";
                        message+="Tu as oublié cette expression multi-mots !";
                        $(container_id+'.container-validated-upl[data-upl-index="'+upl_index+'"]').addClass('almost_correct_upl').removeClass('container-validated-upl').append($('<span class="commentary-upl">').html(message));
                    }
                }
            });
           
        } 
        return next;
    }
    function getUplHtml(words_positions,sentence_id){
        sentence_id = sentence_id || null;
        if(!Array.isArray(words_positions)) 
            words_positions = words_positions.split('-');        
        var upl_html = '';
        var container_sentence_id = (sentence_id)? '#sentence_'+sentence_id+ ' ' : '#sentence ';
        $(words_positions).each(function(index,word_position){
            var word = $(container_sentence_id+'#word_index_'+word_position).html();
            upl_html+='<div class="word-upl" data-word-position="'+word_position+'">'+word+'</div>';
        });
        return upl_html;
    }

    function processJsonAnswerUpl(json, upls_user){
        
        var next = true;

        if(json.experience){
            $('.experience').html(json.experience);
        }
        if(json.money){
            $('.money').html(json.money);
        }
        if(json.samples){
            $('.samples').html(json.samples);
        }
        if(json.score){
            $('.score').html(json.score);
        }
        
        updateProgression(json.turn,json.nb_turns);

        next = displayAnswersUpls(json.correct_upls,upls_user);

        if(json.unknown_upls && json.unknown_upls.length>0){
            next = false;
            $(json.unknown_upls).each(function(i, unknown_upl){
                $('.container-validated-upl[data-upl-index="'+unknown_upl+'"]').addClass('almost_correct_upl');
                $('.container-validated-upl[data-upl-index="'+unknown_upl+'"]').append('<div class="btn-group" data-toggle="buttons"><input type="range" class="form-control col-1 d-inline" name="bet" id="bet" value="5" min="1" max="10"><label class="btn btn-success"><input type="radio" name="'+unknown_upl+'">Confirmer</label><label class="btn btn-success"><input type="radio" name="'+unknown_upl+'">Je ne suis pas sûr</label></div>');
            });
        }
        if(json.likely_upls && json.likely_upls.length>0){
            next = false;
            $(json.likely_upls).each(function(i, likely_upl){
                $('.container-validated-upl[data-upl-index="'+likely_upl+'"]').addClass('almost_correct_upl');
                $('.container-validated-upl[data-upl-index="'+likely_upl+'"]').append('<div class="btn-group" data-toggle="buttons"><input type="range" class="form-control col-1 d-inline" name="bet" id="bet" value="5" min="1" max="10"><label class="btn btn-success"><input type="radio" name="'+likely_upl+'">Upl</label><label class="btn btn-success"><input type="radio" name="'+likely_upl+'">Non Upl</label><label class="btn btn-success"><input type="radio" name="'+likely_upl+'">Je ne sais pas</label></div>');
            });
        }

        if(next){
            $('#new-upl').html('&nbsp;');
            $('#validated-upl').html('');          
            suivant();
        } else {
            stopLoader();
            $('.container-validated-upl').removeClass('container-validated-upl');
            $('#btn-validate-upl').hide();
            if(json.turn == json.nb_turns)
                $('#next-sentence').html('Continuer'); 
            $('#next-sentence').show();     
        }
    }

    function processAfterResponse(json){
        console.log ('[jeu.js] ENTER processAfterResponse42');

        if(json.error){
            alert(json.error);
            var href=window.location.href;
            window.location.href = href;
            return;
        }
        if(json.href){
            window.location.href = json.href;
        }

        var time = 0;
        var addPasser = false;
        if(json.gain != undefined && $("#points_earned").length>0)
            $("#points_earned").html(json.gain);
        if((json.reference&&json.errors)||mode=='training'||mode=='demo'){
            if(jQuery.inArray( json.answer, json.expected_answers ) < 0){
                hideLoader();
                if(json.mode=='special')
                    var attribute = '.disabled-reponse[id_phenomene';
                else
                    var attribute = '.disabled-word[data-word-position';
                if(json.answer=='99999'){
                    var answer_user = img_croix_os;
                } else {
                    var reponse = $(attribute+'=' + json.answer + ']');
                    reponse.removeClass('hover').addClass('not_solution');
                    var answer_user ='<span class="not_solution">' + reponse.text() + '</span>';
                }
                var right = [];
                $.each(json.expected_answers,function(index,expected_answer){
                    if(expected_answer=="99999")
                        right.push(img_croix_os);
                    else {
                        var juste = $(attribute+'=' + expected_answer + ']');
                        juste.addClass('solution');
                        right.push('<span class="solution">' + juste.text() + '</span>');
                    }
                });
                var resultat = '<h4>'+trans('game.bad-answer',{'answer':answer_user,'response':right.join( trans('game.or') )})+'</h4>';
                // if(json.explication)
                    // resultat+='<br/>('+json.explication+')';
                $('#resultat').html(resultat);
                $('#message-object').html('');
                $('#menuObject').hide();
                $('.refuse').hide();
                if (json.errors == 3) {
                    $('#resultat').append('<h4>'+trans('game.no-more-attempt')+'</h4>');
                } else if (json.errors != 3 && json.errors) {
                    var remaining_trials = 3-json.errors;
                    $('#resultat').append('<h4>'+ trans_choice('game.remaining-trials',{'remaining_trials':remaining_trials})+'</h4>');
                }
                if(mode!='demo'){

                    var button = $('<button id="message-button" data-id="'+json.annotation.id+'" data-type="App\\Models\\Annotation" style="position:relative;" class="btn btn-small btn-faded btn-outline btn-green message-button">Discuter de la réponse <span class="badge">'+json.nb_messages+'</button>');
                    // $('#resultat').append('<div><span style="position:relative;"><span id="report-button" style="position:relative;" class="margin-right btn btn-small btn-faded btn-outline btn-green"><span style="color:#B43104" class="glyphicon glyphicon-warning-sign"></span>&nbsp;Je ne suis pas d\'accord</span></div>');
                    $('#resultat').append(button);
                    $('#bottom').after('<div class="row" id="thread" style="position:relative;top:60px;"><div class="col-12 col-lg-10 mx-lg-auto"><span style="display:none;" class="thread" id="thread_'+json.annotation.id+'"></span></div></div>');
                    button.click(showThread);
                    // $('#resultat').append('<div><span style="margin-right:20px;position:relative;"><span id="message-button" data-id="'+json.annotation.id+'" data-type="Annotation" style="position:relative;" class="btn btn-small btn-faded btn-outline btn-green message-button">Discuter de la réponse <span class="badge">'+json.nb_messages+'</span></span></div>');
                    
                }
                if (json.errors == 3) {
                    $('#resultat').append('<a class="link btn btn-small btn-green" href="'+ base_url + 'game'+'"  id="retourMenu" title="'+ trans('game.back-menu')+'">'+trans('game.back-menu')+'</a>');
                }else {
                    $('#resultat').append('<button class="link btn btn-small btn-green" id="next-sentence" title="'+trans('game.next-sentence')+'">'+trans('game.next-sentence')+'</button>');
                }

                $('#sentence').append('<div id="erreur"></div>');
                $('#erreur').fadeOut(800, function(){
                    $('#erreur').remove();
                });
            }else{
                suivant();
            }
        }else{
            if(jQuery.inArray( json.answer, json.expected_answers ) >= 0){
    
                $('#sentence').finish();
                $('#sentence .word').finish();
                $('#sentence').css({
                    'font-size': '1.7em',
                    'opacity' : '1'
                });
                
                if(json.loot && json.loot.id){
                    $('#resultat').html('<h3>' + trans('game.you-found-object',{'name':json.loot.name})+'</h3>');
                    $blockGame.append('<div class="loot"><img src="'+base_url+'img/object/' + json.loot.image + '" /></div>');
                    time += 1600;
                }

                if(!addPasser){
                    if(time == 0){
                        suivant();
                    }else{
                        delay(time);
                    }
                }else{
                    $('#resultat').append('<br/><a class="link" id="next-sentence" title="'+trans('game.next-sentence')+'">'+trans('game.next-sentence')+'</a>');
                }
            }else{
                if(json.guest != undefined){
                    var reponse = $('.word[word_position=' + json.word_position + ']');
                    reponse.addClass('not_solution');
                    var juste = $('.word[word_position=' + json.expected_answer + ']');
                    juste.addClass('solution');
                    $('#resultat').html('<h4>' + trans('game.bad-answer',{'answer':'<span class="not_solution">' + reponse.text() + '</span>','response':'<span class="solution">' + juste.text() + '</span>'})+'</h4>');
                    $('#resultat').append('<a class="link" id="next-sentence" title="'+trans('game.next-sentence')+'">'+trans('game.next-sentence')+'</a>');
                    $('#sentence').append('<div id="erreur"></div>');
                    $('#erreur').fadeOut(800, function(){
                        $('#erreur').remove();
                    });
                }else{
                    suivant();
                }
            }
        }
        $('.word').each(function(){
            $(this).removeClass('word');
        });

    }

    function resizeProgressBar(){
        console.log($('#progressBar').height());
        console.log($('#progressBar').width());        
        $('#progress-container').css({height: 0.98*parseInt($('#progressBar').height(),10)+'px',lineHeight: $('#progressBar').height()+'px'});
        $('#progress').css({height: $('#progressBar').height()+'px',lineHeight: 0.92*parseInt($('#progressBar').height(),10)+'px'});
        $('#phaseBar').css({
            width : progression/100*$('#progressBar').width() + 'px'
        });
    }


