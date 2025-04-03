<!-- /.content-wrapper -->
<footer class="main-footer">
  <strong>&copy; 2024-2025 <a href="">Telecel</a>.</strong> All rights reserved.
  <div class="float-right d-none d-sm-inline-block">
    <b>Version</b>
  </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,dayGridDay'
            },
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap',
            events: 'get_events.php', // URL de ton fichier PHP qui renvoie les événements
            editable: true,
            selectable: true,
            dateClick: function(info) {
                alert('Date cliquée : ' + info.dateStr);
            }
        });
        calendar.render();
    } else {
        console.error("Erreur : l'élément #calendar n'existe pas !");
    }
});

// Gérer le préchargement
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
      let preloader = document.getElementById("preloader");
      if (preloader) {
        preloader.style.display = "none";
      }
    }, 500);
});

$(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
});
</script>
</body>
</html>