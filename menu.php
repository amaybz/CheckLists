<?
class Menu

    public function __construct()
    {
		// Change the line below to your timezone!
	date_default_timezone_set('Australia/Melbourne');
	}

    public function Show($currentPage,$name)
    {
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
          <div class="container-fluid">
            <a class="navbar-brand" href="#">
            <img src="logo.2b1db366.svg" alt="SES Logo" width="30" height="24" class="d-inline-block align-text-top">
            Dapto Check Lists</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarmenu" aria-controls="navbarmenu" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarmenu">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">';
                if($currentPage == 1)
                {
                    echo '<a class="nav-link active" aria-current="page" href="#">Home</a>';
                }
                else {
                    echo '<a class="nav-link" href="#">Home</a>';
                }
                echo '</li>';  
                echo '<li class="nav-item">';
                if($currentPage == 2)
                {
                    echo '<a class="nav-link active" aria-current="page" href="#">Check Lists</a>';
                }
                else {
                    '<a class="nav-link" href="#">Check Lists</a>';
                }   
                echo '</li>';  
                echo '<li class="nav-item">';
                if($currentPage == 3)
                {
                    echo '<a class="nav-link active" aria-current="page" href="#">History</a>';
                }
                else {
                    echo '<a class="nav-link" href="#">History</a>';
                }  
                echo '</li>';                      
               echo '</ul>';
            echo '<ul class="navbar-nav d-flex">'; 
                echo '<li class="nav-item dropdown">';
                echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                echo $name;     
                echo '</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown">';
                    echo '<li><a class="dropdown-item" href="#">Admin</a></li>';          
                    echo '<li><hr class="dropdown-divider"></li>';   
                    echo '<li><a class="dropdown-item" href="#">Logout</a></li>';
                    echo '</ul>';            
                echo '</li>';            
            echo '</ul>'; 
        echo '</div>';
       echo '</div>';  
       echo '</nav>';  
    }
 ?>