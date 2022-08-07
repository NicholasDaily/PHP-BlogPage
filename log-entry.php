<div id="log-entry">
	<form id="log-form" method="POST" enctype="multipart/form-data" action="controllers/log-validation.php">
		<h3>Log entry: <span id="log-update-date"><?php echo date("Y-m-d"); ?></span></h3>
		<div id="main-form-container">
			<div id="description-container">
				<label for="log-description">Description:</label>
				<textarea id="log-description" name="log-description" placeholder="Description of programming session..."></textarea>
			</div>
			<div id="tag-container">
				<label for="tag-text">Tags:</label>
				<textarea id="tag-text" name="tag-text" placeholder="#BackEndDev #sql #tags"></textarea>
			</div>
		</div>
		<div id="bottom">
			<label for="hours">Hours: </label>
			<input type="text" id="hours" name="hours" value="0.00">
			<p id="rate">rate: £12.00</span>
			<p id="log-total">Total: £<span id="log-total-value">0.00</span></p>
			<input type="submit" value="POST">
		</div>
		<script>
			var formElement = document.getElementById("log-form");
			formElement.addEventListener('submit', function(event){
				var formData = new FormData(formElement);
				event.preventDefault();
				sendData(formData);
				
			});

			var logDate = document.getElementById("log-update-date");

			function sendData(formData){
				var logDateText = logDate.innerText;
				var request = new XMLHttpRequest();
				request.open("POST", "controllers/log-validation.php");
				formData.append("entry-date", logDateText);
				request.send(formData);
				
				request.onreadystatechange = function(){
					if(request.readyState === 4){
						if(request.status === 200){
							console.log(request.response);
							document.getElementById("log-description").value = "";
							document.getElementById("tag-text").value = "";
							document.getElementById("hours").value = "0.00";
						}
						}else{
							console.log("Error: " + request.status);
						}
				}
			}
			var hours = document.getElementById("hours");
			hours.addEventListener('keyup', updateTotal);

			function updateTotal(){
				numhours = parseFloat(hours.value);
				document.getElementById("log-total-value").innerText = 12.00 * numhours;
			}


			function updateLog(date, element){
				logDate.innerText = date;
				logElements = element.parentNode.childNodes;
				var duration;
				var description;
				var tags = "";
				for(i = 0; i < logElements.length; i++){
					if(logElements[i].classList == undefined) continue;
					if(logElements[i].classList.contains("date-duration")){
						dateDuration = logElements[i].childNodes;
						for(j = 0; j < dateDuration.length; j++){
							if(dateDuration[j].classList == undefined) continue;
							if(dateDuration[j].classList.contains("duration"))
								duration = dateDuration[j].innerText.replace('hrs', '');
						}
					}
					if(logElements[i].classList.contains("description-tags")){
						descriptionTags = logElements[i].childNodes;
						for(j = 0; j < descriptionTags.length; j++){
							if(descriptionTags[j].classList == undefined) continue;
							if(descriptionTags[j].classList.contains("description"))
								description = descriptionTags[j].innerText;
							if(descriptionTags[j].classList.contains("tags")){
								tagsContainer = descriptionTags[j].childNodes;
								for(k = 0; k < tagsContainer.length; k++){
									tags += tagsContainer[k].innerText + " ";
								}
							}
						
						}
					}
				}
				document.getElementById("log-description").value = description;
				document.getElementById("tag-text").value = tags;
				document.getElementById("hours").value = duration;
			}


		</script>
	</form>
</div>
