<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Gestion_projets</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.1/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
  <!-- FullCalendar -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Custom styles -->

  <!-- Google Font: Source Sans Pro 
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">-->
  <!-- Custom styles -->
      <!-- Inclure les fichiers CSS de Bootstrap et AdminLTE
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="path_to_adminlte.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>-->
    <style>
        /* Supprimer la marge à gauche du conteneur principal */
      

    .modal-content {
      box-shadow: none !important;
    }
    .modal-backdrop {
      background-color: transparent !important;
    }

    #preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #fff;
      z-index: 9999;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    #content {
      display: none;
    }

    #preloader.hidden {
      display: none;
    }

    .header-image-container {
      position: relative;
      width: 100%;
      height: 200px;
      overflow: hidden;
    }

    .header-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .btn-project:hover {
      background-color: #007bff;
      border-color: #0056b3;
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .comment-container {
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #f8f9fa;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .comment-container:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    #vmap {
      width: 600px;
      height: 400px;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
    }

    .content {
      padding: 2rem;
    }

    .form-control:focus, .form-select:focus {
      border-color: #007bff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      outline: none;
    }

    .btn-success:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .table-container {
      max-width: 80%;
      margin-left: auto;
      margin-right: 0;
    }

    
    .header-image-container {
    position: relative;
    background-image: url('assets/images/jte.png'); /* Chemin vers l'image */
    background-size: cover; /* L'image couvre tout le conteneur */
    background-position: center; /* Centrer l'image */
    background-repeat: no-repeat; /* Ne pas répéter l'image */
    height: 300px; /* Hauteur du conteneur */
    width: 100%; /* Largeur du conteneur */
    overflow: hidden; /* Masquer tout ce qui dépasse */
}

.header-image-container::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('assets/images/spider-web.png'); /* Chemin vers l'effet de fil d'araignée */
    background-size: contain; /* Ajuster la taille du motif */
    background-repeat: no-repeat; /* Ne pas répéter le motif */
    background-position: center; /* Centrer le motif */
    opacity: 0.5; /* Rendre le motif semi-transparent */
    pointer-events: none; /* Empêcher toute interaction avec la superposition */
}

.breadcrumb-container {
    display: flex;
    justify-content: flex-end; /* Align the breadcrumb to the right */
}

.breadcrumb {
    display: inline-block; /* Now this will work as intended */
    position: relative;
    z-index: 1;
    padding: 10px 15px;
    border-radius: 5px;
    background-color: transparent;
    box-shadow: none;
    margin: 0;
}

.breadcrumb-item a {
    text-decoration: none;
    color: #007bff;
}

 /*.breadcrumb-item.active {
   color: #6c757d;
}*/


  </style>
</head>
<div class="wrapper">