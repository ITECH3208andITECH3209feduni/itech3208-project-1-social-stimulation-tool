import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import LevelController from "./level.controller.js";
import LevelSchema from "./level.validation.js";

const publicRouter = express.Router();
const adminRouter = express.Router();

// MARK: - PUBLIC ROUTES
publicRouter.get("/", LevelController.getLevels);

// MARK: - ADMIN ROUTES
adminRouter.post(
    "/",
    authMw.authorizeRole([authMw.UserRole.admin]),
    bodyMw.validate(LevelSchema.insert),
    LevelController.insertLevel
);

adminRouter.post(
    "/bulk",
    authMw.authorizeRole([authMw.UserRole.admin]),
    LevelController.bulkInsertLevels
);

export { publicRouter as LevelPublicRouter, adminRouter as LevelAdminRouter };
