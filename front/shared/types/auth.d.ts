declare module '#auth-utils' {
    
    interface User {
      email: string
      roles: string[]
    }
  
    interface SecureSessionData {
      token: string
      expiresAt: number 
    }
  }
  
  export {}