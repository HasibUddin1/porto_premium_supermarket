<form action="core/contact.php" method="post" class="container">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="message">Message:</label>
        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>

    <!-- Contact Form Success or Error Message -->
    <div id="contactResponse">
        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'true') {
                echo '<p style="color: green;">Your Message has been sent!</p>';
            } elseif ($_GET['status'] === 'false') {
                echo '<p style="color: red;">Message sending failed. Please try again later.</p>';
            }
        }
        ?>
    </div>
</form>