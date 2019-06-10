import { StaffingConfiguration } from '../types';
export interface RestCredentials {
    BhRestToken: string;
    restUrl: string;
}
export interface StaffingAuthProvider {
    credential(config: StaffingConfiguration): Promise<RestCredentials>;
}
export declare class StaffingOAuthBaseProvider {
    config: StaffingConfiguration;
    protected validateCode(authCode: any): Promise<any>;
    protected restLogin(accessToken: any): Promise<RestCredentials>;
}
export declare class StaffingCredentialsAuthProvider implements StaffingAuthProvider {
    private readonly username;
    private readonly password;
    constructor(username: string, password: string);
    credential(config: StaffingConfiguration): Promise<RestCredentials>;
}
export declare class StaffingOAuthProvider extends StaffingOAuthBaseProvider implements StaffingAuthProvider {
    credential(config: any): Promise<RestCredentials>;
    private getAuthCode();
}
export declare class StaffingOAuthPopupProvider extends StaffingOAuthBaseProvider implements StaffingAuthProvider {
    config: StaffingConfiguration;
    credential(config: any): Promise<RestCredentials>;
    private getAuthCode(authWindow);
}
