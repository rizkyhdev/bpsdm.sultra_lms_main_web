//enrolled courses
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#coursesTab .nav-link');
    
    tabs.forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent the default anchor behavior

        // Remove active class from all tabs
        tabs.forEach(link => link.classList.remove('active', 'bg-white', 'text-dark'));
        
        // Add active class to the clicked tab
        tab.classList.add('active', 'bg-white', 'text-dark');

        // Here you can also toggle the corresponding content based on the `data-tab` attribute
        const target = tab.getAttribute('data-tab');
        console.log(target); // You can load or show the content accordingly
      });
    });
  });
 
  //enrolled course
document.addEventListener('DOMContentLoaded', function() {
        // Get all the tabs
        const tabs = document.querySelectorAll('#coursesTab .nav-link');

        tabs.forEach(tab => {
            // Make the first tab active by default
            if (tab.classList.contains('active')) {
                tab.classList.add('bg-white', 'text-dark'); // Apply active background to the default tab
            }

            // Add click event listener to each tab
            tab.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default anchor behavior
                
                // Remove 'active' and 'bg-white' from all tabs
                tabs.forEach(link => {
                    link.classList.remove('active', 'bg-white', 'text-dark');
                    link.classList.add('text-white'); // Restore text color to white for non-active tabs
                });

                // Add 'active' and 'bg-white' to the clicked tab
                tab.classList.add('active', 'bg-white', 'text-dark');

                // You can implement additional functionality here like showing/hiding content based on the tab
                const target = tab.getAttribute('data-tab');
                console.log(target); // Log the active tab for demonstration
            });
        });
    });

//active
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('#coursesTab .nav-link');

    tabs.forEach(tab => {
      if (tab.classList.contains('active')) {
        tab.classList.add('bg-white', 'text-dark');
      }

      tab.addEventListener('click', function (e) {
        e.preventDefault();

        tabs.forEach(link => {
          link.classList.remove('active', 'bg-white', 'text-dark');
          link.classList.add('text-white');
        });

        tab.classList.add('active', 'bg-white', 'text-dark');

        const target = tab.getAttribute('data-tab');
        console.log(target);
      });
    });
  });

//complete
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#coursesTab .nav-link');
    
    tabs.forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault(); 
        tabs.forEach(link => link.classList.remove('active', 'bg-white', 'text-dark'));
        
        tab.classList.add('active', 'bg-white', 'text-dark');
        const target = tab.getAttribute('data-tab');
        console.log(target); 
      });
    });
  });
//complete
document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('#coursesTab .nav-link');

        tabs.forEach(tab => {
            
            if (tab.classList.contains('active')) {
                tab.classList.add('bg-white', 'text-dark'); 
            }
            tab.addEventListener('click', function(e) {
                e.preventDefault(); 
                
                tabs.forEach(link => {
                    link.classList.remove('active', 'bg-white', 'text-dark');
                    link.classList.add('text-white'); 
                });
                tab.classList.add('active', 'bg-white', 'text-dark');

                const target = tab.getAttribute('data-tab');
                console.log(target); 
            });
        });
    });

//whislist
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#coursesTab .nav-link');
    tabs.forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault();
        tabs.forEach(link => link.classList.remove('active', 'bg-white', 'text-dark'));
        tab.classList.add('active', 'bg-white', 'text-dark');
        const target = tab.getAttribute('data-tab');
        console.log(target); 
      });
    });
  });

  //whislist
document.addEventListener('DOMContentLoaded', function() {
        // Get all the tabs
        const tabs = document.querySelectorAll('#coursesTab .nav-link');

        tabs.forEach(tab => {
            // Make the first tab active by default
            if (tab.classList.contains('active')) {
                tab.classList.add('bg-white', 'text-dark'); 
            }
            tab.addEventListener('click', function(e) {
                e.preventDefault(); 
                tabs.forEach(link => {
                    link.classList.remove('active', 'bg-white', 'text-dark');
                    link.classList.add('text-white'); 
                });

                tab.classList.add('active', 'bg-white', 'text-dark');

                const target = tab.getAttribute('data-tab');
                console.log(target); 
            });
        });
    });