<div class="contentSection">
    <div class="contentToPrint">
        <!-- content to be printed here -->
    </div>
</div>

<div class="contentSection">
    <a href="#" id="printOut">Print This</a>
</div>

<div class="contentSection termsToPrint">
    <h4>Terms & conditions</h4>
    <p>Management reserves the right to withdraw, amend or suspend this print job in the event of any unforeseen circumstances outside its reasonable control, with no liability to any third party.</p>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>

<script type="text/javascript">
    $(function(){
        $('#printOut').click(function(e){
            e.preventDefault();
            var w = window.open();
            var printOne = $('.contentToPrint').html();
            var printTwo = $('.termsToPrint').html();
            w.document.write('<html><head><title>Copy Printed</title></head><body><h1>Copy Printed</h1><hr />' + printOne + '<hr />' + printTwo) + '</body></html>';
            w.window.print();
            w.document.close();
            return false;
        });
    });
</script>