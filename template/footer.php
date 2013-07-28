	</div>	<hr>
		<footer>
		
        <p>Copyright &copy; 2001-2012, Sphinx Technologies Inc.</p>
      </footer>
	</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	
	
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

<script>
function __highlight(s, t) {
    var matcher = new RegExp("(" + $.ui.autocomplete.escapeRegex(t) + ")", "ig");
    return s.replace(matcher, "<strong>$1</strong>");
}
$(document).ready(
	function() {
	    $("#suggest").autocomplete(
		    {
			source : function(request, response) {
			    $.ajax({
				url : '<?=$ajax_url;?>',
				dataType : 'json',
				data : {
				    term : request.term
				},

				success : function(data) {
				    response($.map(data, function(item) {
					return {
					    label : __highlight(item.label,
						    request.term),
					    value : item.label
					};
				    }));
				}
			    });
			},
			minLength : 3,
			select : function(event, ui) {

			    $('#searchbutton').submit();
			}
		    }).keydown(function(e) {
		if (e.keyCode === 13) {
		    $("#search_form").trigger('submit');
		}
	    }).data("autocomplete")._renderItem = function(ul, item) {

		return $("<li></li>").data("item.autocomplete", item).append(
			$("<a></a>").html(item.label)).appendTo(ul);
	    };
	});
</script>

</body>
</html>