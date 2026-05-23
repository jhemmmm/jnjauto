<template>
    <div>
        <!-- Stats row -->
        <section class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Total Items</div>
                    <div class="h2 fw-bold mb-1">{{ stats.totalItems }}</div>
                    <div class="small text-info fw-bold">
                        <i class="fa-solid fa-boxes-stacked me-1"></i>
                        Tracked products
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Inventory Value</div>
                    <div class="h2 fw-bold mb-1">₱{{ Number(stats.totalValue).toLocaleString("en-PH", { minimumFractionDigits: 2 }) }}</div>
                    <div class="small text-success fw-bold">
                        <i class="fa-solid fa-peso-sign me-1"></i>
                        Total cost value
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Low Stock</div>
                    <div class="h2 fw-bold mb-1" :class="stats.lowStock > 0 ? 'text-warning' : ''">{{ stats.lowStock }}</div>
                    <div class="small text-warning fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Need reorder
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="panel-card rounded-4 p-4 h-100 dash-stat-card">
                    <div class="small text-secondary fw-semibold mb-2">Out of Stock</div>
                    <div class="h2 fw-bold mb-1" :class="stats.outOfStock > 0 ? 'text-danger' : ''">{{ stats.outOfStock }}</div>
                    <div class="small text-danger fw-bold">
                        <i class="fa-solid fa-circle-xmark me-1"></i>
                        Unavailable
                    </div>
                </div>
            </div>
        </section>

        <!-- Filter bar -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Search</label>
                    <input type="text" class="form-control rounded-4" placeholder="Item name or SKU..." v-model="search" @keyup.enter="fetchData" />
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Category</label>
                    <select class="form-select rounded-4" v-model="categoryFilter" @change="fetchData">
                        <option value="all">All Categories</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }} ({{ c.items_count }})</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Status</label>
                    <select class="form-select rounded-4" v-model="statusFilter" @change="fetchData">
                        <option value="all">All Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-info text-white rounded-4 px-3 btn-lg flex-fill fw-semibold btn-gradient" @click="fetchData">
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        Filter
                    </button>
                    <button class="btn btn-light border rounded-4 px-3 btn-lg fw-semibold" @click="resetFilters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Inventory Items -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1">Inventory Items</h3>
                    <p class="small text-secondary mb-0">Manage supplies and track stock levels.</p>
                </div>
                <div class="d-flex gap-2 align-self-start align-self-md-center">
                    <button class="btn btn-sm btn-light border rounded-3 px-3 fw-semibold" @click="openLogsModal">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i>
                        Activity Log
                    </button>
                    <span class="badge text-bg-info rounded-pill px-3 py-2">{{ items.total || 0 }} items</span>
                </div>
            </div>

            <overlay :show="loading">
                <!-- Desktop table -->
                <div v-if="items.data && items.data.length > 0" class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0 admin-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>SKU</th>
                                <th class="text-center">Stock</th>
                                <th>Unit Cost</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items.data" :key="item.id">
                                <td>
                                    <div class="fw-bold">{{ item.name }}</div>
                                    <div class="small text-secondary" v-if="item.description">{{ item.description }}</div>
                                </td>
                                <td>
                                    <span class="badge text-bg-light border rounded-pill px-2 py-1">
                                        {{ item.category?.name || "-" }}
                                    </span>
                                </td>
                                <td>
                                    <code class="small">{{ item.sku || "-" }}</code>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold" :class="stockColor(item)">{{ item.quantity }} {{ item.unit }}</span>
                                    <div class="small text-secondary">Reorder: {{ item.reorder_level }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-info-emphasis">₱{{ Number(item.cost).toLocaleString("en-PH", { minimumFractionDigits: 2 }) }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-2 py-1" :class="statusBadge(item.status)">
                                        {{ statusLabel(item.status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light border rounded-3 px-2" title="Stock In" @click="openStockModal(item, 'stock_in')">
                                            <i class="fa-solid fa-arrow-down text-success"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-3 px-2" title="Stock Out" @click="openStockModal(item, 'stock_out')">
                                            <i class="fa-solid fa-arrow-up text-danger"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-3 px-2" title="Edit" @click="editItem(item)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-3 px-2 text-danger" title="Delete" @click="deleteItem(item)">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile cards -->
                <div v-if="items.data && items.data.length > 0" class="d-block d-md-none">
                    <div class="d-flex flex-column gap-3">
                        <div v-for="item in items.data" :key="'m-' + item.id" class="border rounded-4 p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="fw-bold">{{ item.name }}</div>
                                    <span class="badge text-bg-light border rounded-pill px-2 py-1 small">
                                        {{ item.category?.name || "-" }}
                                    </span>
                                </div>
                                <span class="badge rounded-pill px-2 py-1" :class="statusBadge(item.status)">
                                    {{ statusLabel(item.status) }}
                                </span>
                            </div>
                            <div class="row g-2 small mb-3">
                                <div class="col-4">
                                    <div class="text-secondary">Stock</div>
                                    <div class="fw-bold" :class="stockColor(item)">{{ item.quantity }} {{ item.unit }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-secondary">Cost</div>
                                    <div class="fw-bold text-info-emphasis">₱{{ Number(item.cost).toLocaleString("en-PH", { minimumFractionDigits: 2 }) }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-secondary">SKU</div>
                                    <div class="fw-bold">
                                        <code>{{ item.sku || "-" }}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-light border rounded-3 flex-fill" @click="openStockModal(item, 'stock_in')">
                                    <i class="fa-solid fa-arrow-down text-success me-1"></i>
                                    In
                                </button>
                                <button class="btn btn-sm btn-light border rounded-3 flex-fill" @click="openStockModal(item, 'stock_out')">
                                    <i class="fa-solid fa-arrow-up text-danger me-1"></i>
                                    Out
                                </button>
                                <button class="btn btn-sm btn-light border rounded-3 px-2" @click="editItem(item)">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-sm btn-light border rounded-3 px-2 text-danger" @click="deleteItem(item)">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="!loading && items.data && items.data.length === 0" class="text-center py-5">
                    <i class="fa-solid fa-boxes-stacked fa-3x text-secondary mb-3 d-block"></i>
                    <h5 class="fw-bold">No inventory items found</h5>
                    <p class="text-secondary">Add your first item to start tracking stock.</p>
                </div>

                <!-- Pagination -->
                <nav v-if="items.last_page > 1" class="mt-4">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item" :class="{ disabled: items.current_page === 1 }">
                            <a class="page-link rounded-3" href="#" @click.prevent="goToPage(items.current_page - 1)"><i class="fa-solid fa-chevron-left small"></i></a>
                        </li>
                        <li v-for="p in items.last_page" :key="p" class="page-item" :class="{ active: p === items.current_page }">
                            <a class="page-link rounded-3" href="#" @click.prevent="goToPage(p)">{{ p }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: items.current_page === items.last_page }">
                            <a class="page-link rounded-3" href="#" @click.prevent="goToPage(items.current_page + 1)"><i class="fa-solid fa-chevron-right small"></i></a>
                        </li>
                    </ul>
                </nav>
            </overlay>
        </section>

        <!-- Categories section -->
        <section class="panel-card rounded-4 p-4 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h3 class="h4 fw-bold mb-1">Categories</h3>
                    <p class="small text-secondary mb-0">Organize your inventory items by category.</p>
                </div>
                <span class="badge text-bg-info rounded-pill px-3 py-2 align-self-start align-self-md-center">{{ categories.length }} categories</span>
            </div>

            <div v-if="categories.length > 0" class="table-responsive">
                <table class="table align-middle mb-0 admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th class="text-center">Items</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in categories" :key="'cat-' + c.id">
                            <td>
                                <span class="fw-bold text-info-emphasis">{{ c.id }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ c.name }}</div>
                            </td>
                            <td>
                                <div class="small text-secondary">{{ c.description || "-" }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge text-bg-light border rounded-pill px-2 py-1">{{ c.items_count }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-light border rounded-3 px-2" @click="editCategory(c)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border rounded-3 px-2 text-danger" @click="deleteCategory(c)">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-center py-4">
                <p class="text-secondary mb-0">No categories yet. Create one first to organize items.</p>
            </div>
        </section>

        <!-- ─── MODALS ─── -->

        <!-- Item Modal (Add/Edit) -->
        <div class="modal fade" id="itemModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">{{ itemForm.id ? "Edit Item" : "Add New Item" }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-semibold">
                                    Item Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control rounded-4" v-model="itemForm.name" placeholder="e.g. Car Shampoo" />
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">SKU</label>
                                <input type="text" class="form-control rounded-4" v-model="itemForm.sku" placeholder="e.g. SHP-001" />
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Description</label>
                                <textarea class="form-control rounded-4" rows="2" v-model="itemForm.description" placeholder="Optional description..."></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">
                                    Category
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select rounded-4" v-model="itemForm.category_id">
                                    <option value="">Select category</option>
                                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">
                                    Unit
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select rounded-4" v-model="itemForm.unit">
                                    <option value="pcs">Pieces</option>
                                    <option value="liters">Liters</option>
                                    <option value="gallons">Gallons</option>
                                    <option value="bottles">Bottles</option>
                                    <option value="boxes">Boxes</option>
                                    <option value="packs">Packs</option>
                                    <option value="kg">Kilograms</option>
                                    <option value="rolls">Rolls</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">
                                    Unit Cost (₱)
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control rounded-4" v-model="itemForm.cost" min="0" step="0.01" />
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">
                                    Initial Quantity
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control rounded-4" v-model="itemForm.quantity" min="0" :disabled="!!itemForm.id" />
                                <div v-if="itemForm.id" class="form-text">Use Stock In/Out to change quantity.</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">
                                    Reorder Level
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control rounded-4" v-model="itemForm.reorder_level" min="0" />
                            </div>
                        </div>
                        <div v-if="itemErrors.length" class="alert alert-danger rounded-4 mt-3 mb-0">
                            <div v-for="(e, i) in itemErrors" :key="i" class="small">{{ e }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-4 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-info text-white rounded-4 px-4 btn-gradient fw-semibold" @click="saveItem" :disabled="savingItem">
                            <span v-if="savingItem" class="spinner-border spinner-border-sm me-1"></span>
                            {{ itemForm.id ? "Update Item" : "Create Item" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Modal (Add/Edit) -->
        <div class="modal fade" id="categoryModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">{{ categoryForm.id ? "Edit Category" : "Add New Category" }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                Category Name
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control rounded-4" v-model="categoryForm.name" placeholder="e.g. Cleaning Agents" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Description</label>
                            <textarea class="form-control rounded-4" rows="2" v-model="categoryForm.description" placeholder="Optional description..."></textarea>
                        </div>
                        <div v-if="catErrors.length" class="alert alert-danger rounded-4 mt-3 mb-0">
                            <div v-for="(e, i) in catErrors" :key="i" class="small">{{ e }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-4 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-info text-white rounded-4 px-4 btn-gradient fw-semibold" @click="saveCategory" :disabled="savingCat">
                            <span v-if="savingCat" class="spinner-border spinner-border-sm me-1"></span>
                            {{ categoryForm.id ? "Update Category" : "Create Category" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Adjustment Modal -->
        <div class="modal fade" id="stockModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">
                            <template v-if="stockForm.type === 'stock_in'">
                                <i class="fa-solid fa-arrow-down text-success me-2"></i>
                                Stock In
                            </template>
                            <template v-else-if="stockForm.type === 'stock_out'">
                                <i class="fa-solid fa-arrow-up text-danger me-2"></i>
                                Stock Out
                            </template>
                            <template v-else>
                                <i class="fa-solid fa-sliders text-primary me-2"></i>
                                Adjustment
                            </template>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <div class="panel-card rounded-4 p-3 mb-3 bg-light">
                            <div class="fw-bold">{{ stockForm.itemName }}</div>
                            <div class="small text-secondary">
                                Current stock:
                                <span class="fw-bold" :class="stockForm.currentQty <= 0 ? 'text-danger' : 'text-success'">{{ stockForm.currentQty }} {{ stockForm.unit }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Type</label>
                            <select class="form-select rounded-4" v-model="stockForm.type">
                                <option value="stock_in">Stock In (add)</option>
                                <option value="stock_out">Stock Out (remove)</option>
                                <option value="adjustment">Manual Adjustment (set to)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                Quantity
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control rounded-4" v-model="stockForm.quantity" min="1" />
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Notes</label>
                            <textarea class="form-control rounded-4" rows="2" v-model="stockForm.notes" placeholder="Reason for adjustment..."></textarea>
                        </div>
                        <div v-if="stockErrors.length" class="alert alert-danger rounded-4 mt-3 mb-0">
                            <div v-for="(e, i) in stockErrors" :key="i" class="small">{{ e }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border rounded-4 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-info text-white rounded-4 px-4 btn-gradient fw-semibold" @click="saveStock" :disabled="savingStock">
                            <span v-if="savingStock" class="spinner-border spinner-border-sm me-1"></span>
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs Modal -->
        <div class="modal fade" id="logsModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i>
                            Activity Log
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <overlay :show="loadingLogs">
                            <div v-if="logs.data && logs.data.length > 0" class="table-responsive">
                                <table class="table align-middle mb-0 admin-table small">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Item</th>
                                            <th>Type</th>
                                            <th class="text-center">Qty Change</th>
                                            <th class="text-center">Before → After</th>
                                            <th>User</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="log in logs.data" :key="log.id">
                                            <td class="text-nowrap">{{ formatDate(log.created_at) }}</td>
                                            <td class="fw-bold">{{ log.item?.name || "-" }}</td>
                                            <td>
                                                <span class="badge rounded-pill px-2 py-1" :class="logBadge(log.type)">
                                                    {{ logLabel(log.type) }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold" :class="log.quantity > 0 ? 'text-success' : 'text-danger'">{{ log.quantity > 0 ? "+" : "" }}{{ log.quantity }}</td>
                                            <td class="text-center">{{ log.quantity_before }} → {{ log.quantity_after }}</td>
                                            <td>{{ log.user?.name || "System" }}</td>
                                            <td class="small text-secondary">{{ log.notes || "-" }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-center py-4">
                                <p class="text-secondary mb-0">No activity logs yet.</p>
                            </div>

                            <nav v-if="logs.last_page > 1" class="mt-3">
                                <ul class="pagination pagination-sm justify-content-center mb-0">
                                    <li v-for="p in logs.last_page" :key="'lp' + p" class="page-item" :class="{ active: p === logs.current_page }">
                                        <a class="page-link rounded-3" href="#" @click.prevent="fetchLogs(p)">{{ p }}</a>
                                    </li>
                                </ul>
                            </nav>
                        </overlay>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmModal" ref="deleteConfirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-triangle-exclamation fa-3x text-danger"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ deleteTarget?.type === "category" ? "Delete Category" : "Delete Item" }}</h5>
                        <p class="text-secondary mb-0">
                            Are you sure you want to delete
                            <strong>{{ deleteTarget?.name }}</strong>
                            ? This cannot be undone.
                        </p>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light border rounded-4 px-4 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger rounded-4 px-4 py-2 fw-semibold" @click="confirmDelete" :disabled="deleting">
                            <span v-if="deleting" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="fa-solid fa-trash-can me-2"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
            <div ref="toast" class="toast rounded-4 border-0 shadow-lg" role="alert">
                <div class="toast-body d-flex align-items-center gap-2 px-4 py-3 fw-semibold" :class="toastClass">
                    <i :class="toastIcon" class="me-1"></i>
                    {{ toastMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: false,

            // Data
            items: {},
            categories: [],
            stats: { totalItems: 0, totalValue: 0, lowStock: 0, outOfStock: 0 },

            // Filters
            search: "",
            categoryFilter: "all",
            statusFilter: "all",
            page: 1,

            // Item form
            itemForm: this.emptyItemForm(),
            itemErrors: [],
            savingItem: false,

            // Category form
            categoryForm: { id: null, name: "", description: "" },
            catErrors: [],
            savingCat: false,

            // Stock form
            stockForm: { itemId: null, itemName: "", currentQty: 0, unit: "", type: "stock_in", quantity: 1, notes: "" },
            stockErrors: [],
            savingStock: false,

            // Logs
            logs: {},
            loadingLogs: false,

            // Modal instances
            _itemModal: null,
            _categoryModal: null,
            _stockModal: null,
            _logsModal: null,
            _deleteConfirmModal: null,

            // Delete
            deleteTarget: null,
            deleting: false,

            // Toast
            toastMessage: "",
            toastClass: "text-bg-success",
            toastIcon: "fa-solid fa-circle-check",
        };
    },
    mounted() {
        this.fetchData();
        this.listenPageActions();
    },
    methods: {
        emptyItemForm() {
            return { id: null, category_id: "", name: "", description: "", sku: "", unit: "pcs", cost: 0, quantity: 0, reorder_level: 5 };
        },

        listenPageActions() {
            document.querySelectorAll('[data-action="add-item"]').forEach((btn) => {
                btn.addEventListener("click", () => {
                    this.itemForm = this.emptyItemForm();
                    this.itemErrors = [];
                    this.getModal("itemModal").show();
                });
            });
            document.querySelectorAll('[data-action="add-category"]').forEach((btn) => {
                btn.addEventListener("click", () => {
                    this.categoryForm = { id: null, name: "", description: "" };
                    this.catErrors = [];
                    this.getModal("categoryModal").show();
                });
            });
        },

        getModal(id) {
            const key = "_" + id;
            if (!this[key]) {
                this[key] = new bootstrap.Modal(document.getElementById(id));
            }
            return this[key];
        },

        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.search) params.append("search", this.search);
                if (this.categoryFilter !== "all") params.append("category", this.categoryFilter);
                if (this.statusFilter !== "all") params.append("status", this.statusFilter);
                params.append("page", this.page);

                const { data } = await axios.get("/panel/api/inventory?" + params.toString());
                this.items = data.items;
                this.categories = data.categories;
                this.stats = data.stats;
            } catch (e) {
                this.showToast("Failed to load inventory data.", "danger");
            }
            this.loading = false;
        },

        resetFilters() {
            this.search = "";
            this.categoryFilter = "all";
            this.statusFilter = "all";
            this.page = 1;
            this.fetchData();
        },

        goToPage(p) {
            if (p < 1 || p > this.items.last_page) return;
            this.page = p;
            this.fetchData();
        },

        // ── Item CRUD ──

        editItem(item) {
            this.itemForm = {
                id: item.id,
                category_id: item.category_id,
                name: item.name,
                description: item.description || "",
                sku: item.sku || "",
                unit: item.unit,
                cost: item.cost,
                quantity: item.quantity,
                reorder_level: item.reorder_level,
            };
            this.itemErrors = [];
            this.getModal("itemModal").show();
        },

        async saveItem() {
            this.itemErrors = [];
            this.savingItem = true;
            try {
                if (this.itemForm.id) {
                    await axios.put("/panel/api/inventory/items/" + this.itemForm.id, this.itemForm);
                    this.showToast("Item updated successfully.", "success");
                } else {
                    await axios.post("/panel/api/inventory/items", this.itemForm);
                    this.showToast("Item created successfully.", "success");
                }
                this.getModal("itemModal").hide();
                this.fetchData();
            } catch (e) {
                if (e.response?.status === 422) {
                    const errs = e.response.data.errors || {};
                    this.itemErrors = Object.values(errs).flat();
                } else {
                    this.itemErrors = [e.response?.data?.message || "Something went wrong."];
                }
            }
            this.savingItem = false;
        },

        async deleteItem(item) {
            this.deleteTarget = { id: item.id, name: item.name, type: "item", url: "/panel/api/inventory/items/" + item.id };
            this.getModal("deleteConfirmModal").show();
        },

        // ── Category CRUD ──

        editCategory(c) {
            this.categoryForm = { id: c.id, name: c.name, description: c.description || "" };
            this.catErrors = [];
            this.getModal("categoryModal").show();
        },

        async saveCategory() {
            this.catErrors = [];
            this.savingCat = true;
            try {
                if (this.categoryForm.id) {
                    await axios.put("/panel/api/inventory/categories/" + this.categoryForm.id, this.categoryForm);
                    this.showToast("Category updated successfully.", "success");
                } else {
                    await axios.post("/panel/api/inventory/categories", this.categoryForm);
                    this.showToast("Category created successfully.", "success");
                }
                this.getModal("categoryModal").hide();
                this.fetchData();
            } catch (e) {
                if (e.response?.status === 422) {
                    const errs = e.response.data.errors || {};
                    this.catErrors = Object.values(errs).flat();
                } else {
                    this.catErrors = [e.response?.data?.message || "Something went wrong."];
                }
            }
            this.savingCat = false;
        },

        async deleteCategory(c) {
            this.deleteTarget = { id: c.id, name: c.name, type: "category", url: "/panel/api/inventory/categories/" + c.id };
            this.getModal("deleteConfirmModal").show();
        },

        async confirmDelete() {
            if (!this.deleteTarget) return;
            this.deleting = true;
            try {
                await axios.delete(this.deleteTarget.url);
                this.showToast((this.deleteTarget.type === "category" ? "Category" : "Item") + " deleted successfully.", "success");
                this.fetchData();
            } catch (e) {
                this.showToast(e.response?.data?.message || "Failed to delete.", "danger");
            } finally {
                this.getModal("deleteConfirmModal").hide();
                this.deleting = false;
            }
        },

        // ── Stock Adjustment ──

        openStockModal(item, type) {
            this.stockForm = {
                itemId: item.id,
                itemName: item.name,
                currentQty: item.quantity,
                unit: item.unit,
                type: type,
                quantity: 1,
                notes: "",
            };
            this.stockErrors = [];
            this.getModal("stockModal").show();
        },

        async saveStock() {
            this.stockErrors = [];
            this.savingStock = true;
            try {
                await axios.post("/panel/api/inventory/items/" + this.stockForm.itemId + "/stock", {
                    type: this.stockForm.type,
                    quantity: this.stockForm.quantity,
                    notes: this.stockForm.notes,
                });
                this.showToast("Stock updated successfully.", "success");
                this.getModal("stockModal").hide();
                this.fetchData();
            } catch (e) {
                if (e.response?.status === 422) {
                    const errs = e.response.data.errors || {};
                    this.stockErrors = e.response.data.message ? [e.response.data.message] : Object.values(errs).flat();
                } else {
                    this.stockErrors = ["Something went wrong."];
                }
            }
            this.savingStock = false;
        },

        // ── Logs ──

        openLogsModal() {
            this.getModal("logsModal").show();
            this.fetchLogs(1);
        },

        async fetchLogs(page) {
            this.loadingLogs = true;
            try {
                const { data } = await axios.get("/panel/api/inventory/logs?page=" + page);
                this.logs = data.logs;
            } catch (e) {
                console.error(e);
            }
            this.loadingLogs = false;
        },

        // ── Helpers ──

        statusBadge(status) {
            return (
                {
                    in_stock: "text-bg-success",
                    low_stock: "text-bg-warning",
                    out_of_stock: "text-bg-danger",
                }[status] || "text-bg-secondary"
            );
        },

        statusLabel(status) {
            return (
                {
                    in_stock: "In Stock",
                    low_stock: "Low Stock",
                    out_of_stock: "Out of Stock",
                }[status] || status
            );
        },

        stockColor(item) {
            if (item.quantity <= 0) return "text-danger";
            if (item.quantity <= item.reorder_level) return "text-warning";
            return "text-success";
        },

        logBadge(type) {
            return (
                {
                    stock_in: "text-bg-success",
                    stock_out: "text-bg-danger",
                    adjustment: "text-bg-primary",
                }[type] || "text-bg-secondary"
            );
        },

        logLabel(type) {
            return (
                {
                    stock_in: "Stock In",
                    stock_out: "Stock Out",
                    adjustment: "Adjustment",
                }[type] || type
            );
        },

        formatDate(dt) {
            if (!dt) return "-";
            const d = new Date(dt);
            return d.toLocaleDateString("en-PH", { month: "short", day: "numeric", year: "numeric" }) + " " + d.toLocaleTimeString("en-PH", { hour: "2-digit", minute: "2-digit" });
        },

        showToast(message, type = "success") {
            this.toastMessage = message;
            this.toastClass = type === "success" ? "text-bg-success" : "text-bg-danger";
            this.toastIcon = type === "success" ? "fa-solid fa-circle-check" : "fa-solid fa-circle-xmark";
            const toast = new bootstrap.Toast(this.$refs.toast, { delay: 3000 });
            toast.show();
        },
    },
};
</script>
