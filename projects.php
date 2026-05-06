<?php
// Include database connection
require_once 'includes/db_connect.php';

// Retrieve all project categories
$cat_stmt = $conn->prepare("SELECT * FROM project_categories ORDER BY id ASC");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve projects along with their categories and associated programming languages
$proj_query = "
    SELECT p.*, pc.category_name, 
           GROUP_CONCAT(l.lang_name SEPARATOR '||') as lang_names,
           GROUP_CONCAT(l.icon_slug SEPARATOR '||') as lang_icons
    FROM projects p 
    JOIN project_categories pc ON p.category_id = pc.id 
    LEFT JOIN project_languages pl ON p.id = pl.project_id
    LEFT JOIN languages l ON pl.language_id = l.id
    GROUP BY p.id 
    ORDER BY p.id DESC
";
$proj_stmt = $conn->prepare($proj_query);
$proj_stmt->execute();
$projects = $proj_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="projects" class="page-section">
    <div class="section-wrapper">
        
        <div class="section-heading">
            <p class="sub-title">My Recent Work</p>
            <h2 class="main-title">Featured Projects</h2>
        </div>

        <!-- Category filter buttons -->
        <div class="project-filters">
            <button class="p-filter-btn active" data-filter="all">All</button>
            <?php foreach($categories as $cat): ?>
                <?php $filter_slug = strtolower(str_replace(' ', '-', $cat['category_name'])); ?>
                <button class="p-filter-btn" data-filter="<?php echo $filter_slug; ?>">
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Projects grid container -->
        <div class="project-grid">
            <?php foreach($projects as $proj): ?>
                <?php $cat_slug = strtolower(str_replace(' ', '-', $proj['category_name'])); ?>
                
                <div class="project-card" data-category="<?php echo $cat_slug; ?>" onclick="openProjectLink(event, '<?php echo htmlspecialchars($proj['project_link']); ?>')">
                    
                    <!-- Project thumbnail image with zoom hover effect -->
                    <div class="p-card-img">
                        <img src="uploads/projects/<?php echo $proj['image_path']; ?>" alt="<?php echo htmlspecialchars($proj['title']); ?>">
                    </div>

                    <!-- Project content/details section -->
                    <div class="p-card-body">
                        
                        <div class="p-header-row">
                            <h3 class="p-title"><?php echo htmlspecialchars($proj['title']); ?></h3>
                            
                            <!-- Status label and live preview button -->
                            <div class="header-right-actions">
                                <?php if(!empty($proj['status'])): ?>
                                    <span class="p-status"><?php echo htmlspecialchars($proj['status']); ?></span>
                                <?php endif; ?>
                                
                                <?php if(!empty($proj['project_link'])): ?>
                                    <!-- Live project button -->
                                    <a href="<?php echo htmlspecialchars($proj['project_link']); ?>" target="_blank" class="live-action-btn" onclick="event.stopPropagation();">
                                        <span class="pulse-indicator"></span> Live
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="p-desc"><?php echo htmlspecialchars($proj['description']); ?></p>

                        <!-- Technologies used in the project -->
                        <div class="p-tech-section">
                            <h4>Technologies:</h4>
                            <div class="tech-icons-row">
                                <?php 
                                if(!empty($proj['lang_icons'])): 
                                    $icons = explode('||', $proj['lang_icons']);
                                    $names = explode('||', $proj['lang_names']);
                                    for($i = 0; $i < count($icons); $i++):
                                ?>
                                    <div class="tech-item">
                                        <img src="https://skillicons.dev/icons?i=<?php echo trim($icons[$i]); ?>" alt="">
                                        <span><?php echo trim($names[$i]); ?></span>
                                    </div>
                                <?php 
                                    endfor;
                                endif; 
                                ?>
                            </div>
                        </div>

                        <!-- Toggle button to expand/collapse additional content -->
                        <button class="show-more-toggle" onclick="toggleFeatures(event, this)">
                            <span>Show More</span> <i class="fas fa-chevron-down"></i>
                        </button>

                        <!-- Hidden expandable section -->
                        <div class="p-expandable-content" style="display: none;">
                            <div class="p-features">
                                <h4>KEY FEATURES</h4>
                                <?php 
                                if(!empty($proj['key_features'])):
                                    $features = explode(',', $proj['key_features']);
                                    foreach($features as $feature):
                                        if(trim($feature) !== ''):
                                ?>
                                    <div class="feature-box"><?php echo htmlspecialchars(trim($feature)); ?></div>
                                <?php 
                                        endif;
                                    endforeach;
                                endif; 
                                ?>
                            </div>
                            
                            <div class="p-links">
                                <?php if(!empty($proj['github_link'])): ?>
                                    <!-- GitHub repository link -->
                                    <a href="<?php echo $proj['github_link']; ?>" target="_blank" class="p-btn p-btn-code" onclick="event.stopPropagation();">
                                        <i class="fab fa-github" style="font-size: 1.1rem;"></i> Source Code
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if(empty($projects)): ?>
                <!-- Message displayed when no projects exist -->
                <p style="text-align: center; color: #94a3b8; grid-column: 1 / -1;">No projects available yet.</p>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ==========================================
     CSS FOR PROJECTS SECTION
=========================================== -->
<style>
    .page-section { padding: 100px 0; background: #0b1120; }
    .section-wrapper { width: 85%; max-width: 1300px; margin: 0 auto; }
    
    .project-filters { display: flex; justify-content: center; gap: 15px; margin-bottom: 40px; flex-wrap: wrap; }
    .p-filter-btn {
        background: transparent; border: 1px solid #334155; color: #cbd5e1;
        padding: 8px 24px; border-radius: 30px; font-size: 0.95rem; cursor: pointer; transition: 0.3s ease;
    }
    .p-filter-btn.active, .p-filter-btn:hover { background: #818cf8; border-color: #818cf8; color: #fff; }

    .project-grid { 
        display: grid; 
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px; 
        align-items: start; 
    }

    .project-card {
        background: #1e293b; border-radius: 20px; overflow: hidden;
        border: 1px solid #334155; box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer; 
    }
    .project-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.5); border-color: #475569; }

    /* Project image container with zoom hover effect */
    .p-card-img { 
        height: 230px; 
        width: 100%; 
        overflow: hidden; 
        border-bottom: 1px solid #334155;
    }
    .p-card-img img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover;
        object-position: top center; 
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .p-card-img:hover img { 
        transform: scale(1.15);
    }

    .p-card-body { padding: 25px; }

    /* Header section with title, status, and action buttons */
    .p-header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; gap: 10px; }
    .p-title { color: #f8fafc; font-size: 1.3rem; font-weight: 700; line-height: 1.3; margin: 0; }
    
    .header-right-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
    
    .p-status {
        background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid #10b981;
        padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; letter-spacing: 0.5px; white-space: nowrap;
    }

    /* Live status button with pulse animation */
    .live-action-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(239, 68, 68, 0.1); color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3); padding: 4px 10px;
        border-radius: 20px; font-size: 0.75rem; font-weight: 700;
        text-decoration: none; transition: 0.3s;
    }
    .live-action-btn:hover { background: rgba(239, 68, 68, 0.2); color: #f87171; border-color: #f87171; }
    
    /* Pulse indicator animation */
    .pulse-indicator {
        width: 8px; height: 8px; background-color: #ef4444; border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); animation: pulse-red 2s infinite;
    }

    .p-desc { color: #cbd5e1; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px; }

    .p-tech-section h4 { color: #f8fafc; font-size: 0.9rem; margin-bottom: 12px; font-weight: 600; }
    .tech-icons-row { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
    .tech-item { display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .tech-item img { width: 28px; height: 28px; }
    .tech-item span { color: #94a3b8; font-size: 0.7rem; font-weight: 500; }

    /* Expand/collapse toggle button */
    .show-more-toggle {
        background: none; border: none; color: #818cf8; font-size: 0.9rem; font-weight: 600;
        cursor: pointer; padding: 0; display: flex; align-items: center; gap: 8px; transition: 0.3s;
    }

    /* Expandable content section */
    .p-expandable-content { margin-top: 20px; border-top: 1px solid #334155; padding-top: 20px; cursor: default; }
</style>

<!-- ==========================================
     JAVASCRIPT FOR FILTERING, TOGGLE & CLICK
=========================================== -->
<script>
    // Handle project card click navigation
    function openProjectLink(event, url) {
        // Prevent navigation when interacting with inner controls
        if(event.target.closest('.show-more-toggle') || event.target.closest('.p-expandable-content') || event.target.closest('.live-action-btn')){
            return; 
        }
        
        // Open project link in a new tab if available
        if(url && url.trim() !== '') {
            window.open(url, '_blank');
        }
    }

    // Toggle expandable features section
    function toggleFeatures(event, btn) {
        event.stopPropagation(); 

        const content = btn.nextElementSibling;
        const textSpan = btn.querySelector('span');
        const icon = btn.querySelector('i');

        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
            textSpan.textContent = "Show Less";
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            content.style.display = "none";
            textSpan.textContent = "Show More";
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    // Initialize category filtering functionality
    document.addEventListener("DOMContentLoaded", function() {
        const filterBtns = document.querySelectorAll('.p-filter-btn');
        const projectCards = document.querySelectorAll('.project-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filterValue = btn.getAttribute('data-filter');

                projectCards.forEach(card => {
                    if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
</script>