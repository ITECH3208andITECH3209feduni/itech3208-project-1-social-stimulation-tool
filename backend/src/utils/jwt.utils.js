import jwt from "jsonwebtoken";
import { envConfig } from "#config/index.js";

const ACCESS_TOKEN_SECRET = envConfig.accessToken;
const REFRESH_TOKEN_SECRET = envConfig.refreshToken;
const ACCESS_TOKEN_EXPRIRE_TIME = "50m";
const REFRESH_TOKEN_EXPRIRE_TIME = "7d";

/**
 * Generate an access token
 * @param {Object} payload - The user data to encode
 * @param {string} expiresIn - Token expiration time
 * @returns {string} - The generated access token
 */
const generateAccessToken = (payload, expiresIn = ACCESS_TOKEN_EXPRIRE_TIME) => {
    return jwt.sign(payload, ACCESS_TOKEN_SECRET, { expiresIn });
};

/**
 * Generate a refresh token
 * @param {Object} payload - The user data to encode
 * @param {string} expiresIn - Token expiration time
 * @returns {string} - The generated refresh token
 */
const generateRefreshToken = (payload, expiresIn = REFRESH_TOKEN_EXPRIRE_TIME) => {
    return jwt.sign(payload, REFRESH_TOKEN_SECRET, { expiresIn });
};

/**
 * Verify a JWT token
 * @param {string} token - The token to verify
 * @param {string} secret - The secret key (access or refresh token)
 * @returns {Object|null} - Decoded token data if valid, otherwise null
 */
const verifyToken = (token, secret) => {
    try {
        return jwt.verify(token, secret);
    } catch (error) {
        return null; // Token is invalid or expired
    }
};

const jwtUtil = {
    genAccessToken: generateAccessToken,
    genRefreshToken: generateRefreshToken,
    verifyToken: verifyToken,
};

export default jwtUtil;
