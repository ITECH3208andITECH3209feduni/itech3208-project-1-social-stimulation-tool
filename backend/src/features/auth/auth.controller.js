import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import AuthService from "./auth.service.js";
import AuthMessages from "./auth.message.js";

const AuthController = {
    registerUser: async (req, res) => {
        try {
            const payload = {
                username: req.body.username,
                email: req.body.email,
                password: req.body.password,
                confirmedPassword: req.body.confirmedPassword,
                phone: req.body.phone,
            };

            const result = await AuthService.registerUser(payload);

            resUtil.sendSuccess({
                res,
                message: AuthMessages.success.REGISTER_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[AuthController.registerUser]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    loginUser: async (req, res) => {
        try {
            const input = {
                username: req.body.username,
                password: req.body.password,
            };

            const result = await AuthService.loginUser(input);

            resUtil.sendSuccess({
                res,
                message: AuthMessages.success.LOGIN_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[AuthController.loginUser]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default AuthController;
