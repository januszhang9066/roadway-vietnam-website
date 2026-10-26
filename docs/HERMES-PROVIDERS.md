# Hermes Agent 内置 Provider 清单(源码实测)
# 来源: ~/.hermes/hermes-agent/hermes_cli/auth.py  _REGISTRY_ROWS
# 共 34 个 api_key/OAuth provider + 5 个 OAuth 流程 provider

## 老板问的 5 家 —— 全部已内置

| 厂商 | Hermes provider id | 类型 | 内置 base_url | 环境变量 |
|---|---|---|---|---|
| **Gemini** | `gemini` | api_key | `https://generativelanguage.googleapis.com/v1beta` | `GOOGLE_API_KEY` / `GEMINI_API_KEY` |
| | `vertex` | vertex | 运行时按 project_id+region 算 | |
| | `ai-gateway` | api_key | `https://ai-gateway.vercel.sh/v1` | `AI_GATEWAY_API_KEY` |
| **OpenAI** | `openai-codex` | **oauth_external** | Codex 专用路由 | ChatGPT 订阅 OAuth |
| | `openai-api` | api_key | `https://api.openai.com/v1` | `OPENAI_API_KEY` |
| **Qwen** | `qwen-oauth` | **oauth_external** | `DEFAULT_QWEN_BASE_URL` | OAuth 登录 |
| | `alibaba-coding-plan` | api_key | `https://coding-intl.dashscope.aliyuncs.com/v1` | `ALIBABA_CODING_PLAN_API_KEY` |
| | `alibaba` | api_key | `https://dashscope-intl.aliyuncs.com/compatible-mode/v1` | `DASHSCOPE_API_KEY` |
| **DeepSeek** | `deepseek` | api_key | `https://api.deepseek.com/v1` | `DEEPSEEK_API_KEY` |
| **Kimi** | `kimi-coding` | api_key | `https://api.moonshot.ai/v1` | `KIMI_API_KEY` / `KIMI_CODING_API_KEY` |
| | `kimi-coding-cn` | api_key | `https://api.moonshot.cn/v1` | `KIMI_CN_API_KEY` |

**关键**:`kimi-coding` 的 `_resolve_kimi_base_url` 会把 `sk-kimi-` 开头的 key
自动重定向到 `api.kimi.com/coding` —— 即 **Kimi Coding Plan 的额度可以直接用在 Hermes 上**。

## 完整 provider 列表(34 个)

```
nous              Nous Portal         oauth_device_code
openai-codex       OpenAI Codex        oauth_external
xai-oauth         xAI Grok OAuth      oauth_external
qwen-oauth        Qwen OAuth          oauth_external
minimax-oauth     MiniMax OAuth       oauth_minimax

openai-api        OpenAI API
gemini            Google AI Studio
zai               Z.AI / GLM
kimi-coding       Kimi / Moonshot
kimi-coding-cn    Kimi / Moonshot (China)
stepfun           StepFun Step Plan
arcee             Arcee AI
gmi               GMI Cloud
actual            Actual Computer
minimax           MiniMax
minimax-cn        MiniMax (China)
anthropic         Anthropic           (含 CLAUDE_CODE_OAUTH_TOKEN)
alibaba           Qwen Cloud
alibaba-coding-plan  Alibaba Cloud (Coding Plan)
deepseek          DeepSeek
xai               xAI
nvidia            NVIDIA NIM
ai-gateway        Vercel AI Gateway
opencode-zen      OpenCode Zen
opencode-go       OpenCode Go
kilocode          Kilo Code
huggingface       Hugging Face
xiaomi            Xiaomi MiMo
tencent-tokenhub  Tencent TokenHub
tencent-tokenplan Tencent TokenPlan
copilot           GitHub Copilot
copilot-acp       GitHub Copilot ACP
lmstudio          LM Studio
ollama-cloud      Ollama Cloud
bedrock           AWS Bedrock
vertex            Google Vertex AI
azure-foundry     Azure Foundry
```

## 对别名的支持(老板敲命令时能用的名字)

```
glm / z-ai / z.ai / zhipu        → zai
google / google-gemini / google-ai-studio → gemini
x-ai / x.ai / grok                → xai
kimi / kimi-for-coding / moonshot → kimi-coding
kimi-cn / moonshot-cn             → kimi-coding-cn
step / stepfun-coding-plan        → stepfun
claude / claude-code              → anthropic
github / github-copilot           → copilot
chatgpt / chatgpt-codex           → openai-codex
qwen-portal / qwen-cli            → qwen-oauth
alibaba_coding / alibaba_coding_plan → alibaba-coding-plan
tokenplan / tencent-lkeap         → tencent-tokenplan
opencode / zen                    → opencode-zen
kilo / kilo-code                  → kilocode
local / ollama / vllm / llamacpp  → custom
```

## 实操要点

1. **`hermes model` 是交互式选择器** —— 不是 `hermes models list`
   跑 `hermes model` 会列出所有 provider 让你选,`--refresh` 强制拉最新模型列表。

2. **Claude Code 的 OAuth token 能直接复用**
   `CLAUDE_CODE_OAUTH_TOKEN`(sk-ant-oat01... 开头)已在 anthropic 的发现列表里,
   Hermes 会按前缀自动走 OAuth 路由,不用单独配。

3. **Qwen 有两条路**
   - `qwen-oauth`:OAuth 登录,免 key
   - `alibaba-coding-plan`:Coding Plan 的 API key,base_url 是 `coding-intl.dashscope.aliyuncs.com`
   注意这两个的计费池是分开的。

4. **Kimi Coding Plan 的 key 直接能用**
   `KIMI_CODING_API_KEY` + `sk-kimi-` 前缀 → 自动切到 coding 端点。

5. **fallback_providers 可以配多个**
   `config.yaml` 里 `fallback_providers: []`,主模型挂了自动切备胎。
