function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.visibility = "hidden";  

  if (currentPage === 'home') {
    document.getElementById("main-content").style.marginLeft = "50px";
} else {
    document.getElementById("main-content").style.marginLeft = "250px";
}
  
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.visibility = "visible";  
  document.getElementById("main-content").style.marginLeft = "0";
  
}