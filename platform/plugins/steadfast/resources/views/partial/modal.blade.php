<!-- Modal Structure -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Search and Select</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Search Input with Icon -->

        <div class="search-input d-flex align-items-center" style="position: relative; width: 100%;">
            <input type="text" class="form-control" name="number" id="searchInput" placeholder="Search..." style="flex-grow: 1;">
            <button class="btn search-btn d-flex justify-content-center align-items-center" id="searchBtn">
                <i class="fa fa-search m-0 p-0" aria-hidden="true"></i>
            </button>
        </div>


        <div class="mt-3 row">
            <div class="col-md-6">
                <label class="d-flex form-check">
                    <input class="form-check-input" name="cms"  type="checkbox" id="checkBox1">
                    <span class="form-check-icon"><i class="fas fa-check"></i></span>
                    <span class="form-check-label">CMS</span>
                </label>
            </div>
            <div class="col-md-6">
                <label class="d-flex form-check">
                    <input class="form-check-input" name="api" type="checkbox" id="checkBox2">
                    <span class="form-check-icon"><i class="fas fa-check"></i></span>
                    <span class="form-check-label">API</span>
                </label>
            </div>
        </div>

        <!-- Result Section -->
        <div class="result-section mt-4">
           
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
