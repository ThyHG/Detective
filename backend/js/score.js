$(document).ready(function() {

	update();

});

function update(){

	$.getJSON("data/score.json")
		.done(function(data){

			//sort docu: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/sort
			//sort scores descending
			var temp = data.sort(function(a, b){

				//change position
				if (a.score < b.score) {
					return 1;
				}
				if (a.score > b.score) {
					return -1;
				}
				
				//position stays
				return 0;
			});

			//console.log(temp);
			var table = $("<table/>", { id: "scores" });

			$.each(temp, function(index, obj){

				var tr = $("<tr/>");
		
				var td_id = $("<td/>").append(obj.id);
				var td_score = $("<td/>").append(obj.score);

				tr.append(td_id, td_score);

				table.append(tr);
			});

			//$("#scores").empty();
			$("#scores").replaceWith(table);

		})
		.fail(function(){
			$("notice").text("Game isn't running");
		});

	setTimeout(update, 5000);
}