import { StatusCodes } from "http-status-codes";
import { envConfig } from "#config/index.js";
import { jwtUtil, resUtil } from "#utils/index.js";

const UserRole = {
    individual: "individual",
    organization: "organization",
    admin: "admin",
};

const authenticateToken = (req, res, next) => {
    const header = req.headers.authorization;
    if (!header || !header.startsWith("Bearer ")) {
        return resUtil.sendError({
            res,
            message: "Acess denied: No token provided",
            statusCode: StatusCodes.FORBIDDEN,
        });
    }

    const token = header.split(" ")[1];
    const decodedToken = jwtUtil.verifyToken(token, envConfig.accessTokenSecretKey);

    if (!decodedToken) {
        return resUtil.sendError({
            res,
            message: "Invalid or expired token",
            statusCode: StatusCodes.FORBIDDEN,
        });
    }

    req.user = decodedToken;
    next();
};

const authorizeRole = (roles = []) => {
    return (req, res, next) => {
        authenticateToken(req, res, () => {
            if (!roles.includes(req.user.role)) {
                return resUtil.sendError({
                    res,
                    message: "Access Denied: Insufficient permissions",
                    statusCode: StatusCodes.FORBIDDEN,
                });
            }
            next();
        });
    };
};

const authMw = {
    UserRole,
    authenticateToken,
    authorizeRole,
};

export default authMw;
