// MARK: - PLEASE DO NOT DELETE THIS FILE
import axios from "axios";
import { envConfig } from "#config/index.js";

// MARK: - Create Atlas Instance
console.log(`${envConfig.mongoApiUrl}/${envConfig.atlasProjectId}`);
const atlas = axios.create({
    baseURL: `${envConfig.mongoApiUrl}/${envConfig.atlasProjectId}`,
    headers: {
        "Content-Type": "application/json",
        Authorization: `Basic ${Buffer.from(
            `${envConfig.atlasPublicKey}:${envConfig.atlasPrivateKey}`,
        ).toString("base64")}`,
    },
});

// MARK: - Get Public IP
const getPublicIP = async () => {
    const ipApiUrl = "https://api.ipify.org/?format=json";
    const res = await axios.get(ipApiUrl);
    return res.data.ip;
};

// MARK: - Add IP in Whitelist
const MS_PER_SECOND = 1000;
const SECONDS_PER_MINUTE = 60;
const MINUTES_PER_HOUR = 60;
const MS_PER_HOUR = MS_PER_SECOND * SECONDS_PER_MINUTE * MINUTES_PER_HOUR;
const DEFAULT_TIME_LIVE_HOURS = 8;

const addIP = async (ip, hours = DEFAULT_TIME_LIVE_HOURS) => {
    const deleteAfterDate = new Date(Date.now() + hours * MS_PER_HOUR);
    await atlas.post(
        "/accessList",
        {
            auth: {
                username: envConfig.atlasPublicKey,
                password: envConfig.atlasPrivateKey,
            },
        },
        [
            {
                ipAddress: ip,
                comment: "Scenari-Aid Dev Auto Whitelist",
                deleteAfterDate,
            },
        ],
    );
};

// MARK: - Remove IP
const removeIP = async (ip) => {
    await atlas.delete(`/accessList/${ip}`);
};

const ipAccess = {
    getPublicIP,
    addIP,
    removeIP,
};

export default ipAccess;
