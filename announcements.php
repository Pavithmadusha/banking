<!-- Header -->
<header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Announcements</h1>
        </div>
    </div>
</header>

<!-- Section -->
<section class="py-5">
    <div class="container">
        <div class="card rounded-0 shadow-lg">
            <div class="card-body p-4">
               <?php
               $qry = $conn->query("SELECT * FROM `announcements` order by unix_timestamp(date_created) desc");
               while($row = $qry->fetch_assoc()):
                    $row['announcement'] = strip_tags(stripslashes(html_entity_decode($row['announcement'])));
                ?>
                <a class="card announcement-item text-dark card-outline card-primary mb-3 view_data" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' data-title='<?php echo $row['title'] ?>'>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?php echo $row['title'] ?></h5>
                        <span class="text-muted small"><?php echo date("M d, Y h:i A", strtotime($row['date_created'])) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="truncate mb-0"><?php echo $row['announcement'] ?></p>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>

<!-- Custom Styles -->
<style>
    /* Truncate long announcement text */
    .truncate {
        display: -webkit-box;
        -webkit-line-clamp: 3; /* Number of lines before truncating */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Styling the announcement cards */
    .announcement-item {
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #007bff; /* Outline the cards with primary color */
        border-radius: 0.25rem;
    }

    /* Hover effect for announcement cards */
    .announcement-item:hover {
        background-color: #f8f9fa;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Add some padding to card-body */
    .card-body {
        padding: 1.5rem;
    }

    /* Make announcement header more visually appealing */
    .card-header h5 {
        font-weight: bold;
        font-size: 1.25rem;
    }

    /* Ensure consistent padding for all cards */
    .announcement-item .card-header {
        background-color: #ffffff; 
        border-bottom: none; 
    }

    /* Adjust text color for better readability */
    .card-title, .text-muted {
        color: #333333;
    }
</style>

<script>
    $(function(){
        $('.view_data').click(function(){
            uni_modal($(this).attr('data-title'),'./view_accouncement.php?id='+$(this).attr('data-id'),'mid-large')
        })
    })
</script>
