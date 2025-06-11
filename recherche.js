const searchBar = document.getElementById('searchBar');
const contactTable = document.getElementById('contactTable');


searchBar.addEventListener('keyup',function(){

    const serchValue = searchBar.value.toLowerCase();
    const rows=contactTable.getElementsByTagName('tr');
    for (let i =0; i<rows.length;i++){
        const row= rows[i];
        const rowText= row.textContent.toLowerCase();
        if(rowText.includes(serchValue)){
            row.style.display='';
        }else {
            row.style.display='none';
        }
    }

} ); 