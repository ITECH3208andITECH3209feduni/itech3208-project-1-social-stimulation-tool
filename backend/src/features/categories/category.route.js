import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import CategoryController from "./category.controller.js";
import CategorySchema from "./category.validation.js";

const publicRouter = express.Router();
const adminRouter = express.Router();

// MARK: - PUBLIC ROUTES
publicRouter.get("/", CategoryController.getCategories);

// MARK: - ADMIN ROUTES
adminRouter.post(
    "/",
    authMw.authorizeRole([authMw.UserRole.admin]),
    bodyMw.validate(CategorySchema.insert),
    CategoryController.insertCategory
);

adminRouter.post(
    "/bulk",
    authMw.authorizeRole([authMw.UserRole.admin]),
    CategoryController.bulkInsertCategories
);

adminRouter.delete(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    CategoryController.deleteCategory
);

export { publicRouter as CategoryPublicRouter, adminRouter as CategoryAdminRouter };
