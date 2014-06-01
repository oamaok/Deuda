$(document).ready(function(){
    $("#search").keydown(doSearch);
    $(document).click(function(){
        $("#search-results").empty();
    });
    searchResultTemplate = $("#search-result-template").clone().attr("id", "").css("display", "block");
});

var searchTimeout;
var searchResultTemplate;
var searchResultFocus = -1;
function doSearch(e)
{
    var results = $(".search-result");
    if(e.which == 13)
    {
        e.preventDefault();
        if(searchResultFocus != - 1)
            results[searchResultFocus].click();
    }
    if(e.which == 40 || e.which == 38)
    {
        e.preventDefault();
        if(searchResultFocus != - 1)
            results[searchResultFocus].removeAttribute("selected");
        if(e.which == 40)
            searchResultFocus = (searchResultFocus + 1) % results.length;
        if(e.which == 38)
            searchResultFocus = (searchResultFocus - 1 + results.length) % results.length;
        results[searchResultFocus].setAttribute("selected","");
        return;
    }
    searchResultFocus = -1;
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(search, 50);
}

function search()
{
    var query = $("#search").val();
    $.get("search/" + query, function(response){
        $("#search-results").empty();
        console.log(response);
        var result;
        for(var i = 0; i < response.length; i++)
        {
            result = searchResultTemplate.clone();
            if(i == 0)
                result.attr("class", result.attr("class") + " top");
            result.attr("href", "user/" + response[i].id)
            var reg = new RegExp("(" + query + ")", 'ig');
            var username = response[i].username.replace(reg, "<b>$1</b>");
            var firstName = response[i].firstName.replace(reg, "<b>$1</b>");
            var lastName = response[i].lastName.replace(reg, "<b>$1</b>");
            result.find(".search-username").html(username);
            result.find(".search-fullname").html(firstName + " " + lastName);
            $("#search-results").append(result);
        }
        result.attr("class", result.attr("class") + " bottom");
    });
}