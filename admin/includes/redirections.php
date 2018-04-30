<?php
    function redirectToHome() {
        $jsRedirect = "<script type='text/javascript'>location.href='home.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToAddPage() {
        $jsRedirect = "<script type='text/javascript'>location.href='add.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToVotePage() {
        $jsRedirect = "<script type='text/javascript'>location.href='vote.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToEditPage() {
        $jsRedirect = "<script type='text/javascript'>location.href='edit.php'</script>";
        echo $jsRedirect;
        return;
    }

    function redirectToReviewPage() {
        $jsRedirect = "<script type='text/javascript'>location.href='review.php'</script>";
        echo $jsRedirect;
        return;
    } 