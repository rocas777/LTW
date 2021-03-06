let form_c = document.querySelector("form[id='ask_question']");
form_c.addEventListener('submit',submitComment);

function submitComment(evt){
    evt.preventDefault();

    let _petId = escapeHtml(document.querySelector("input[name='petId']").value);
    let _userId = escapeHtml(document.querySelector("input[name='userId']").value);
    let _comment_text = escapeHtml(document.querySelector("textarea[name='comment_text']").value);
    let _csrf = escapeHtml(document.querySelector("input[name='csrf']").value)

    let request = new XMLHttpRequest();
    request.addEventListener("load",receiveComments)
    request.open("post","../actions/add_comment_action.php",true);
    request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    request.send(encodeForAjax({petId: _petId, userId: _userId, comment_text: _comment_text, csrf: _csrf}));

}

function receiveComments(evt){
    if(this.responseText == 'true'){
        alert("Error Adding Question!");
        return false;
    }
    let parsed_response = JSON.parse(this.responseText);
    let newArticle = document.createElement("article");
    newArticle.className="question";

    let newspan, newCont;

    newspan = document.createElement("a");
    newspan.id="author";
    newspan.href="user.php?user="+parsed_response['userName'];
    newCont = document.createTextNode(escapeHtml(parsed_response['userName'] + ' asked: '));
    newspan.appendChild(newCont);
    newArticle.appendChild(newspan);

    newspan = document.createElement("span");
    newspan.className="date";
    newCont = document.createTextNode(escapeHtml(format_time(parsed_response['date'])));
    newspan.appendChild(newCont);
    newArticle.appendChild(newspan);

    newspan = document.createElement("p");
    newspan.id = "question_text";
    newCont = document.createTextNode(escapeHtml(parsed_response['comment_txt']));
    newspan.appendChild(newCont);
    newArticle.appendChild(newspan);

    let parent = document.querySelector("section[id='questions']");
    let after = document.querySelector("form[id='ask_question']");
    parent.insertBefore(newArticle,after);

    //document.getElementById("comment_submit_message").innerHTML="";
    document.querySelector("textarea[name='comment_text']").value="";


}