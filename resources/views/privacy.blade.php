<!DOCTYPE html>
<html lang="gr">

@include('includes.head')

<body>
    @include('navbar')

    <div id="main-content" style="margin-left: 2em; margin-right: 2em;">
        <h1>eHoreca Project Privacy Policy</h1>

        <h2>Υλικολογισμικό</h2>
        <p>
            Η σελίδα αυτή παρουσιαζει το υλικολογισμικό που αφορά πρόγραμμα "eHoreca".
            <br>
            <br>
            Σχετικά με τις εφαρμογές: <br>
            Οι εφαρμογές έξυπνων κινητών τηλεφώνων iOs και Android δίνουν την δυνατότητα στον χρήστη να συνεισφέρει στην συλλογή πληθοπορισμικής πληροφορίας.
            Ο χρήστης έχει την δυνατότητα να δει την τοποθεσία των κάδων σε χάρτη.
            Επίσης μπορεί να ανεβάζει αναφορές σχετικά με τους κάδους που να συνοδεύονται από φωτογραφίες και απαντήσεις σε
            ερωτηματολόγιο σχετικά με την κατάστασή τους. <br>
            <br>
            Η πολιτική ιδιωτικότητας για τις εφαρμογές έξυπνων κινητών τηλεφώνων iOs και android του έργου “eHoreca” <br>
            Οι πληροφορίες που συλλέγονται μέσω των εν λόγω εφαρμογών είναι οι εξής:
        </p>

        <ul>
            <li>Όνομα χρήστη,</li>
            <li>Επώνυμο χρήστη,</li>
            <li>Διεύθυνση ηλεκτρονικής αλληλογραφίας χρήστη,</li>
            <li>Φωτογραφιες που μεταφορτώνονται μαζί με τις αναφορές,</li>
            <li>Οι συντεταγμένες γεωτοποθεσίας του χρήστη την στιγμή που στέλνει την αναφορά.</li>
        </ul>

        <p>
            Όλη η πληροφορία που συλλέγεται από τους χρήστες είναι απόρρητη και είναι διαθέσιμη μόνο στον υπεύθυνο του έργου
            και υπό καμία περίπτωση δεν χρησιμοποιούνται έξω από τις δράσεις του έργου.
            Καμία άλλη πληροφορία δεν συλλέγεται από τον χρήστη στο υπόβαθρο κατά την εκτέλεση των εφαρμογών. <br>
            <br>
            Για περισσότερες πληροφορίες σχετικά με την πολιτική ιδιωτικότητας παρακαλώ επικοινωνήστε με τον αναπληρωτή
            καθηγητή <a href="http://di.ionio.gr/en/faculty-staff/markos-avlonitis/">Μάρκο Αυλωνίτη</a>.
        </p>

        <h2>Hardware and Software</h2>

        <p>
            The privacy policy of the mobile applications for both iOs and android of the “eHoreca” project. <br>
            The information being collected through those apps are the following:
        </p>

        <ul>
            <li>user’s name,</li>
            <li>user’s surname,</li>
            <li>user’s email address,</li>
            <li>photographs uploaded in the reports,</li>
            <li>user’s geolocation coordinates at the time of the report being sent.</li>
        </ul>

        <p>
            We acknowledge that all the information gathered from the users is private and accessible only to the person in-charge of the “eHoreca” project.
            Under no circumstances these information are being used outside the scope of the project. No data other than the
            aforementioned are being gathered regarding the user in the background. <br>
            <br>
            For more information on the privacy policy of the smartphone apps please contact as-sociate professor <a
                href="http://di.ionio.gr/en/faculty-staff/markos-avlonitis/">Markos Avlonitis</a>.
        </p>
    </div>

    <script>
        $(document).ready(function() {
            $("#main-content").height($(window).height() - $(".navbar-fixed-top").height() - $("#footer").height() - 70);
            $("#main-content").css('margin-top', $(".navbar-fixed-top").height() + 20);
        });
    </script>

</body>

@include('footer')

</html>
