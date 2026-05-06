import express from "express";
import LevelController from "./level.controller.js";
import bodyMw from "#middlewares/body.middleware.js";
import LevelSchema from "./level.validation.js";

const router = express.Router();

router.post("/insert-level", bodyMw.validate(LevelSchema.insert), LevelController.insertLevel);
router.post("/bulk-insert-levels", LevelController.bulkInsertLevels);

export default router;
