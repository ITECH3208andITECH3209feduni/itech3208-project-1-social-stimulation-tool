import express from "express";
import { bodyMw } from "#middlewares/index.js";
import AuthSchema from "./auth.validation.js";
import AuthController from "./auth.controller.js";

const router = express.Router();

router.post(
    "/register-user",
    bodyMw.validate(AuthSchema.registerUser),
    AuthController.registerUser,
);

export default router;
