//Petite fonction javascript pour afficher en temps réel les jeux correspondant à la recherche pendant que l'on écrit sur le clavier
function search_filter() { 
    let input = document.getElementById('searchbar').value;
    input = input.toLowerCase();
    let x = document.getElementsByClassName('postContainer');

    for(i=0; i < x.length; i++) {
        debugger;
        let title = x[i].children[1];
        let desc = x[i].children[2]
        if(!desc.innerHTML.toLowerCase().includes(input) && !title.innerHTML.toLowerCase().includes(input)) {
            x[i].style.display="none";
        }
        else {
            x[i].style.display="unset";
        }
    }
}
