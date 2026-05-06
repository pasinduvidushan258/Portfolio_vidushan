<?php
// Include database connection
require_once 'includes/db_connect.php';

// Fetch all skill categories from the database
$cat_stmt = $conn->prepare("SELECT * FROM skill_categories ORDER BY id ASC");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all skills along with their associated category names
$skill_stmt = $conn->prepare("SELECT s.*, c.category_name 
                              FROM skills s 
                              LEFT JOIN skill_categories c ON s.category_id = c.id 
                              ORDER BY s.id ASC");
$skill_stmt->execute();
$skills = $skill_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Skills Section -->
<section class="tech-skills-section">
    <div class="skills-header">
        <p class="sub-title">Explore My</p>
        <h2 class="main-title">Tech Skills</h2>
    </div>

    <!-- Filter buttons for skill categories -->
    <div class="skills-filter-container">
        <button class="filter-btn active" data-filter="all">All Skills</button>
        <?php foreach($categories as $cat): ?>
            <?php $filter_slug = strtolower(str_replace(' ', '-', $cat['category_name'])); ?>
            <button class="filter-btn" data-filter="<?php echo $filter_slug; ?>">
                <?php echo htmlspecialchars($cat['category_name']); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Skills grid container -->
    <div class="skills-grid-box">
        <div class="skills-grid">
            <?php foreach($skills as $skill): ?>
                <?php $cat_slug = strtolower(str_replace(' ', '-', $skill['category_name'])); ?>
                
                <!-- Individual skill item -->
                <div class="tech-skill-item" data-category="<?php echo $cat_slug; ?>" data-name="<?php echo htmlspecialchars($skill['skill_name']); ?>">
                    <img src="https://skillicons.dev/icons?i=<?php echo htmlspecialchars($skill['icon_slug']); ?>" alt="<?php echo htmlspecialchars($skill['skill_name']); ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CSS for Skills Section -->
<style>
    .tech-skills-section {
        padding: 80px 20px;
        text-align: center;
        background-color: #0b1120;
        color: #fff;
        font-family: 'Poppins', sans-serif;
    }
    
    .skills-header .sub-title {
        color: #94a3b8;
        font-size: 1.2rem;
        margin-bottom: 5px;
        letter-spacing: 1px;
    }
    .skills-header .main-title {
        font-size: 3.5rem;
        color: #818cf8; 
        margin-bottom: 50px;
        font-weight: 700;
    }
    
    .skills-filter-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 50px;
    }
    .filter-btn {
        background-color: transparent;
        color: #cbd5e1;
        border: 1px solid #334155;
        padding: 12px 30px;
        border-radius: 30px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .filter-btn:hover {
        background-color: #1e293b;
        border-color: #818cf8;
        color: #fff;
    }
    .filter-btn.active {
        background-color: #818cf8;
        color: #fff;
        border-color: #818cf8;
        box-shadow: 0 4px 15px rgba(129, 140, 248, 0.4);
    }

    /* Container for skills grid */
    .skills-grid-box {
        background-color: #1e293b;
        border-radius: 25px;
        padding: 40px 50px;
        width: fit-content; 
        max-width: 90%;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    
    /* Flexible layout for skill items */
    .skills-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 60px; 
        max-width: 900px; 
    }

    /* Individual skill card */
    .tech-skill-item {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        height: 100px;
        background-color: #0f172a;
        border: 2px solid #334155; 
        border-radius: 22px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tech-skill-item img {
        width: 65px; 
        height: 65px;
        transition: transform 0.3s ease;
    }

    /* Hover animation for skill item */
    .tech-skill-item:hover {
        border-color: #818cf8; 
        background-color: rgba(129, 140, 248, 0.1);
        transform: translateY(-8px);
        box-shadow: 0 10px 20px rgba(129, 140, 248, 0.2);
    }

    /* Tooltip showing skill name */
    .tech-skill-item::after {
        content: attr(data-name);
        position: absolute;
        top: -45px;
        background: #818cf8;
        color: #fff;
        font-size: 0.9rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transform: translateY(15px);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); 
        box-shadow: 0 4px 15px rgba(129, 140, 248, 0.4);
        pointer-events: none;
        z-index: 10;
    }
    
    /* Tooltip arrow indicator */
    .tech-skill-item::before { 
        content: '';
        position: absolute;
        top: -15px;
        border: 7px solid transparent;
        border-top-color: #818cf8;
        opacity: 0;
        visibility: hidden;
        transform: translateY(15px);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: none;
        z-index: 10;
    }

    /* Show tooltip on hover */
    .tech-skill-item:hover::after,
    .tech-skill-item:hover::before {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
</style>

<!-- JavaScript for filtering skills -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const skillItems = document.querySelectorAll('.tech-skill-item');

        // Handle category filter button clicks
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Update active button state
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filterValue = btn.getAttribute('data-filter');

                // Show/hide skills based on selected category
                skillItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    
                    if (filterValue === 'all' || itemCategory === filterValue) {
                        item.style.display = 'flex'; 
                    } else {
                        item.style.display = 'none'; 
                    }
                });
            });
        });
    });
</script>