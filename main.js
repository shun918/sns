$(function(){

//グローバル変数
var nowModalSyncer = null ;		//現在開かれているモーダルコンテンツ
var modalClassSyncer = "modal-syncer" ;		//モーダルを開くリンクに付けるクラス名

//モーダルのリンクを取得する
var modals = document.getElementsByClassName( modalClassSyncer ) ;

    //モーダルウィンドウを出現させるクリックイベント
    for(var i=0,l=modals.length; l>i; i++){

        //全てのリンクにタッチイベントを設定する
        modals[i].onclick = function(){

            //ボタンからフォーカスを外す
            this.blur() ;

            //ターゲットとなるコンテンツを確認
            var target = this.getAttribute( "data-target" ) ;

            //ターゲットが存在しなければ終了
            if( typeof( target )=="undefined" || !target || target==null ){
                return false ;
            }

            //コンテンツとなる要素を取得
            nowModalSyncer = document.getElementById( target ) ;

            //ターゲットが存在しなければ終了
            if( nowModalSyncer == null ){
                return false ;
            }

            //キーボード操作などにより、オーバーレイが多重起動するのを防止する
            if( $( "#modal-overlay" )[0] ) return false ;		//新しくモーダルウィンドウを起動しない
            //if($("#modal-overlay")[0]) $("#modal-overlay").remove() ;		//現在のモーダルウィンドウを削除して新しく起動する

            //オーバーレイを出現させる
            $( "body" ).append( '<div id="modal-overlay"></div>' ) ;
            $( "#modal-overlay" ).fadeIn( "fast" ) ;

            //コンテンツをセンタリングする
            centeringModalSyncer() ;

            //コンテンツをフェードインする
            $( nowModalSyncer ).fadeIn( "slow" ) ;

            //[#modal-overlay]、または[#modal-close]をクリックしたら…
            $( "#modal-overlay,.modal-close" ).unbind().click( function() {

                //[#modal-content]と[#modal-overlay]をフェードアウトした後に…
                $( "#" + target + ",#modal-overlay" ).fadeOut( "fast" , function() {

                    //[#modal-overlay]を削除する
                    $( '#modal-overlay' ).remove() ;

                } ) ;

                //現在のコンテンツ情報を削除
                nowModalSyncer = null ;

            } ) ;

        }

    }
    
    //リサイズされたら、センタリングをする関数[centeringModalSyncer()]を実行する
    $( window ).resize( centeringModalSyncer ) ;
    
    //センタリングを実行する関数
    function centeringModalSyncer() {

        //画面(ウィンドウ)の幅、高さを取得
        var w = $( window ).width() ;
        var h = $( window ).height() ;

        // コンテンツ(#modal-content)の幅、高さを取得
        // jQueryのバージョンによっては、引数[{margin:true}]を指定した時、不具合を起こします。
//		var cw = $( "#modal-content" ).outerWidth( {margin:true} );
//		var ch = $( "#modal-content" ).outerHeight( {margin:true} );
        var cw = $( ".modal-content" ).outerWidth();
        var ch = $( ".modal-content" ).outerHeight();

        //センタリングを実行する
        $( ".modal-content" ).css( {"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"} ) ;

    }
    
} ) ;

$(function(){

    //モーダルウィンドウを出現させるクリックイベント
    $("#modal-open").click( function(){
    
        //キーボード操作などにより、オーバーレイが多重起動するのを防止する
        $( this ).blur() ;	//ボタンからフォーカスを外す
        if( $( "#modal-overlay" )[0] ) return false ;		//新しくモーダルウィンドウを起動しない (防止策1)
        //if($("#modal-overlay")[0]) $("#modal-overlay").remove() ;		//現在のモーダルウィンドウを削除して新しく起動する (防止策2)
    
        //オーバーレイを出現させる
        $( "body" ).append( '<div id="modal-overlay"></div>' ) ;
        $( "#modal-overlay" ).fadeIn( "slow" ) ;
    
        //コンテンツをセンタリングする
        centeringModalSyncer() ;
    
        //コンテンツをフェードインする
        $( "#modal-share" ).fadeIn( "slow" ) ;
    
        //[#modal-overlay]、または[.modal-close]をクリックしたら…
        $( "#modal-overlay,.modal-close" ).unbind().click( function(){
    
            //[#modal-share]と[#modal-overlay]をフェードアウトした後に…
            $( "#modal-share,#modal-overlay" ).fadeOut( "slow" , function(){
    
                //[#modal-overlay]を削除する
                $('#modal-overlay').remove() ;
    
            } ) ;
    
        } ) ;
    
    } ) ;
    
    //リサイズされたら、センタリングをする関数[centeringModalSyncer()]を実行する
    $( window ).resize( centeringModalSyncer ) ;
    
    //センタリングを実行する関数
    function centeringModalSyncer() {

        //画面(ウィンドウ)の幅、高さを取得
        var w = $( window ).width() ;
        var h = $( window ).height() ;

        // コンテンツ(#modal-content)の幅、高さを取得
        // jQueryのバージョンによっては、引数[{margin:true}]を指定した時、不具合を起こします。
//		var cw = $( "#modal-content" ).outerWidth( {margin:true} );
//		var ch = $( "#modal-content" ).outerHeight( {margin:true} );
        var cw = $( "#modal-share" ).outerWidth();
        var ch = $( "#modal-share" ).outerHeight();

        //センタリングを実行する
        $( "#modal-share" ).css( {"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"} ) ;

    }

} ) ;

$(function(){
    var $good = $('.btn-good'), //いいねボタンセレクタ
                goodPostId; //投稿ID
    $good.on('click',function(e){
        e.stopPropagation();
        var $this = $(this);
        //カスタム属性（postid）に格納された投稿ID取得
        goodPostId = $this.parents('.post').data('postid'); 
        goodname = $this.parents('.post').data('name');
        var data = { postId: goodPostId, name: goodname } 
        $.ajax({
            type: 'POST',
            url: 'ajaxGood.php', //post送信を受けとるphpファイル
            data: data //{キー:投稿ID}
        }).done(function(data){
            console.log('Ajax Success');

            // いいねの総数を表示
            $this.children('span').html(data);
            // いいね取り消しのスタイル
            $this.children('i').toggleClass('far'); //空洞
            // いいね押した時のスタイル
            $this.children('i').toggleClass('fas'); //塗りつぶし
            $this.children('i').toggleClass('active');
            $this.toggleClass('active');
        }).fail(function(msg) {
            console.log('Ajax Error');
        });
    });
});

$(function(){//ページトップへもどる
  $('#js-page-top').on('click', function () {
    $('body,html').animate({
      scrollTop: 0
    }, 300);
    return false;
  });
})