<div class="modal fade" id="moveUndefinedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Перенос объявления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="adId" name="ad_id">
                    <input type="hidden" id="adCategoryId">
                    <input type="hidden" id="adCategoryParentId">

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="categoryLevel1" class="form-label">Категория</label>
                            <a href="javascript:void(0);" class="btn btn-sm add-category-level-1 small">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"></path>
                                </svg>
                                Добавить
                            </a>
                        </div>
                        <select class="form-select" id="categoryLevel1" name="category_level_1">
                        </select>
                    </div>

                    <div class="mb-3 d-none">
                        <label for="newCategoryLevel1" class="form-label">Новая категория</label>
                        <input type="text" class="form-control" id="newCategoryLevel1" name="new_category_level_1">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="categoryLevel2" class="form-label">Подкатегория</label>
                            <a href="javascript:void(0);" class="btn btn-sm add-category-level-2 small">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"></path>
                                </svg>
                                Добавить
                            </a>
                        </div>


                        <select class="form-select" id="categoryLevel2" name="category_level_2">

                        </select>
                    </div>

                    <div class="mb-3 d-none">
                        <label for="newCategoryLevel2" class="form-label">Новая подкатегория</label>
                        <input type="text" class="form-control" id="newCategoryLevel2" name="new_category_level_2">
                    </div>

                    <div class="mb-3">
                        <label for="synonyms" class="form-label">Синонимы (через запятую)</label>
                        <input type="text" class="form-control" id="synonyms" name="synonyms">
                    </div>
                </form>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div></div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary save" >Сохранить</button>
            </div>
        </div>
    </div>
</div>