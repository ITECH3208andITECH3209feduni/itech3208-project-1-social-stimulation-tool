import express from "express";
import CategoryController from "./category.controller.js";
import bodyMw from "#middlewares/body.middleware.js";
import categorySchema from "./category.validation.js";

const router = express.Router();

router.get("/", CategoryController.getCategories);
router.post("/insert-category", bodyMw.validate(categorySchema), CategoryController.insertCategory);
router.post("/bulk-insert-categories", CategoryController.bulkInsertCategories);
router.delete("/delete-category/:id", CategoryController.deleteCategory)

export default router;
